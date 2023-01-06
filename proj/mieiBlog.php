<?php 
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

function getresult($sql){
	//funzione per estrarre risultato query
	global $link;
	$r = mysqli_query($link, $sql);
	if ($r){
		return mysqli_fetch_assoc($r);
	}
	return false;
}

function getresults($sql){
	//funzione per estrarre risultato query
	global $link;
	$r = mysqli_query($link, $sql);
	$res = [];
	while ($a = mysqli_fetch_assoc($r))
		$res [] = $a;
	return $res;
}

//raccolta dati
$id = $_SESSION['idUtente']; //id utente
$username = $_SESSION['nomeUtente']; //nome utente

//query che tira fuori i blog di cui è proprietario o coautore l'utente
$query = "SELECT idBlog FROM partecipanti WHERE nomeUtente = '".addslashes($username)."'";
$result = mysqli_query($link,$query);
$idBlog = [];
while($riga = mysqli_fetch_assoc($result)){
	$idBlog[] = $riga["idBlog"];
};
$datiBlog = []; //array per i dati del blog
$argomenti = []; //array per gli argomenti
foreach ($idBlog as $i) { //scorro gli id dei blog di cui l'utente è partecipante
	//estrazione dei blog dell'utente
	$query2 = "SELECT * FROM blog WHERE id = '".addslashes($i)."' ORDER BY dataCreazione DESC"; 
	$result2 = mysqli_query($link,$query2);
	while($riga2 = mysqli_fetch_assoc($result2)){
		//estrazione argomenti e sottoargomenti del blog da tabelle argomenti e argomentiblog
		//ricavo i sottoargomenti (idParent != 0)
		$querySub = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($i)."' AND argomenti.idParent != 0";
		$resultSub = mysqli_query($link,$querySub);
		$args = [];
		$subs = [];
		while($rigaSub = mysqli_fetch_assoc($resultSub)){ 
			$subs[] = $rigaSub["idParent"];
			//sottoargomento ha per forza argomento
			//ricavo gli argomenti per i sottoargomenti estratti (idParent = 0)
			$queryArg = "SELECT DISTINCT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($i)."' AND argomenti.idParent = 0 AND argomenti.id = '".$rigaSub["idParent"]."'";
			$resultArg = mysqli_query($link,$queryArg);
			while($rigaArg = mysqli_fetch_assoc($resultArg)){ 
				$args[] = $rigaArg['argomento']." ".$rigaSub['argomento']; //salvo argomento e sottoargomento estratto in variabile
			}
		}
		//estraggo argomenti senza sottoargomento
		//se id dell'argomento non è contenuto nell'array sottoargomenti come idParent di qualche sottoargomento vuol dire che è un argomento senza sottoargomenti
		$sqlArg = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($i)."' AND argomenti.idParent = 0 ";
		if(!empty($subs)){ //se nel blog ho degli argomenti con sottoargomento
			$sqlArg .= "AND argomenti.id NOT IN (" . implode( ", " , $subs ) . ")"; 
		}
		$resultSqlArg = mysqli_query($link,$sqlArg);
		if($resultSqlArg){
			while($rigaSqlArg = mysqli_fetch_assoc($resultSqlArg)){
				$args[] = $rigaSqlArg["argomento"];
			}
		}
		$riga2['argomenti'] = implode (', ', $args);
		$datiBlog[] = $riga2; //salvo dati tutti insieme in array datiBlog
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="animations.js"></script>
		<meta charset="utf-8">
	</head>
	<header>
		<nav>
			<ul>
				<li><a class="links" href="logout.php">Logout</a></li>
				<li><a class="links" href="modificaAccount.php">Il mio Account</a></li>
				<li><a class="links" href="home.php">Home</a></li>
				<li><form name="searchForm" method="get" action="searchResults.php"><div class="ricerca"><input type="text" id="inputSearchBar" name="search" class="input corti search" placeholder="Cerca Blog, Utenti, Post, .." autocomplete="off" pattern=".{2,}" title="Minimo due caratteri." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Cerca Blog, Utenti, Post, ..'" required><button type="submit" class="button searchButton" id="buttonSearchBar"><i class="fa fa-search"></i></button></div></form></li>
			</ul> 
		</nav>
	</header>
	<body>
		<div class="alertContainer">
			<form name="form2" method="post" action="updateAccount.php" 
						class="<?php 
						if(isset($_GET["success"])){
							echo "success";
						} else {
							echo "";
						}; 
						?>">
						<?php 
						//gestione alert
						if(isset($_GET["success"])){
							if($_GET["success"]=="registrazione"){
								echo "<div class='successReg'>Registrazione effettuata, benvenuto ".htmlentities($_SESSION["nomeUtente"])."</div>";
							}
							if($_GET["success"]=="delBlog"){
								echo "<div class='successReg'>Blog '".htmlentities($_GET["name"]) ."' eliminato con successo</div>"; 
							}
						}
						?>
			</form>
		</div>
		<h2 class="titles">I Miei Blog</h2>
		<p class="sottotitolo">Qui sotto sono mostrati i blog di cui sei proprietario o coautore<br> Selezionane uno per visualizzarlo</p>
		<div class="externalCont">
			<div class="contenitore">    
				<table class="tableResults">
					<thead>
						<tr>
							<th>Titolo</th>
							<th>Autore</th>
							<th>Data Creazione</th>
							<th>Argomenti</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//estrazione blog dell'utente 
						if($datiBlog){ //se esistono blog
							for($i=0;$i<sizeof($datiBlog);$i++){ //scorro i blog
								$id = htmlentities($datiBlog[$i]["id"]); //id del blog
								?> 
								<tr onclick="window.location.href='blog.php?idBlog=<?=$id?>'">
									<td>
									<?php   
									echo htmlentities($datiBlog[$i]["nomeBlog"]);
									?>
									</td>
									<td>
									<?php   
									echo htmlentities($datiBlog[$i]["autore"]);
									?>
									</td>
									<td>
									<?php
									$date = htmlentities($datiBlog[$i]["dataCreazione"]);
									$timestamp = strtotime($date);
									echo date('d/m/Y', $timestamp);
									?>
									</td>
									<td>
									<?= htmlentities($datiBlog[$i]["argomenti"])?>
									</td>
									<td>
									<?php
									$descrizione = $datiBlog[$i]["descrizione"];
									if(strlen($descrizione)>30){ //preview descrizione
										echo substr($descrizione, 0, 30). "...";
									} else {
										echo htmlentities($descrizione);  
									}
									?>
									</td>
								</tr>
							<?php
							}
							?>
					</tbody>    
				</table>
			</div>
			<?php
			} else { 
			?>
					</tbody>
				</table>
				<p>Non sei risultato proprietario di nessun blog, <a href="creaBlog.php">creane uno ora</a></p>
			</div>
			<?php
			}	
			?>
			<div class="buttonContainer">
				<div class="infoText">Hai eseguito l'accesso come: <b style="color: #212529;"><?php if(!empty($username)){echo htmlentities($username);}?></b></div>
				<button class="button white" style="margin-right: 0;" onclick="window.location.href='creaBlog.php'"> + Crea Nuovo Blog</button>
			</div>
		</div>
	</body>
</html>