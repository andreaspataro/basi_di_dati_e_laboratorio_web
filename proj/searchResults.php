<?php 
require_once "config.php";
require_once "connect.php";

function getresult($sql){
	//funzione per estrarre risultato query
	global $link;
	$r = mysqli_query($link, $sql);
	if ($r){
		return mysqli_fetch_assoc($r);
	}
	return false;
}

function getBlogData($id){
	//funzione che estrae i dati inerenti all'id blog ricevuto come parametro
	global $link;
	//query di estrazione dati del blog
	$query = "SELECT * FROM blog WHERE id = '".addslashes($id)."'";
	$result = mysqli_query($link,$query);
	while($riga = mysqli_fetch_assoc($result)){
		//estrazione argomenti e sottoargomenti del blog da tabelle argomenti e argomentiblog
		//ricavo i sottoargomenti (idParent != 0)
		$querySub = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($id)."' AND argomenti.idParent != 0";
		$resultSub = mysqli_query($link,$querySub);
		$args = [];
		$subs = [];
		while($rigaSub = mysqli_fetch_assoc($resultSub)){ 
			$subs[] = $rigaSub["idParent"];
			//sottoargomento ha per forza argomento
			//ricavo gli argomenti per i sottoargomenti estratti (idParent = 0)
			$queryArg = "SELECT DISTINCT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($id)."' AND argomenti.idParent = 0 AND argomenti.id = '".$rigaSub["idParent"]."'";
			$resultArg = mysqli_query($link,$queryArg);
			while($rigaArg = mysqli_fetch_assoc($resultArg)){ 
				$args[] = $rigaArg['argomento']." ".$rigaSub['argomento']; //salvo argomento e sottoargomento estratto in variabile
			}
		}
		//estraggo argomenti senza sottoargomento
		//se id dell'argomento non è contenuto nell'array sottoargomenti come idParent di qualche sottoargomento vuol dire che è un argomento senza sottoargomenti
		$sqlArg = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($id)."' AND argomenti.idParent = 0 ";
		if(!empty($subs)){ //se nel blog ho degli argomenti con sottoargomento
			$sqlArg .= "AND argomenti.id NOT IN (" . implode( ", " , $subs ) . ")"; 
		}
		$resultSqlArg = mysqli_query($link,$sqlArg);
		if($resultSqlArg){
			while($rigaSqlArg = mysqli_fetch_assoc($resultSqlArg)){ 
				$args[] = $rigaSqlArg["argomento"];
			}
		}
		$riga['argomenti'] = implode (', ', $args);
		$datiBlog = $riga; //salvo dati tutti insieme in array datiBlog
	}
	return $datiBlog;
}

function getAllBlogs(){
	//funzione che estrae tutti i blog nel db
	global $link;
	//query di estrazione dei dati di tutti i blog nel db
	$query = "SELECT * FROM blog";
	$result = mysqli_query($link,$query);
	while($riga = mysqli_fetch_assoc($result)){
		//estrazione argomenti e sottoargomenti del blog da tabelle argomenti e argomentiblog
		//ricavo i sottoargomenti (idParent != 0)
		$querySub = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($riga["id"])."' AND argomenti.idParent != 0";
		$resultSub = mysqli_query($link,$querySub);
		$args = [];
		$subs = [];
		while($rigaSub = mysqli_fetch_assoc($resultSub)){ 
			$subs[] = $rigaSub["idParent"];
			//sottoargomento ha per forza argomento
			//ricavo gli argomenti per i sottoargomenti estratti (idParent = 0)
			$queryArg = "SELECT DISTINCT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($riga["id"])."' AND argomenti.idParent = 0 AND argomenti.id = '".$rigaSub["idParent"]."'";
			$resultArg = mysqli_query($link,$queryArg);
			while($rigaArg = mysqli_fetch_assoc($resultArg)){ 
				$args[] = $rigaArg['argomento']." ".$rigaSub['argomento']; //salvo argomento e sottoargomento estratto in variabile
			}
		}
		//estraggo argomenti senza sottoargomento
		//se id dell'argomento non è contenuto nell'array sottoargomenti come idParent di qualche sottoargomento vuol dire che è un argomento senza sottoargomenti
		$sqlArg = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($riga["id"])."' AND argomenti.idParent = 0 ";
		if(!empty($subs)){ //se nel blog ho degli argomenti con sottoargomento
			$sqlArg .= "AND argomenti.id NOT IN (" . implode( ", " , $subs ) . ")"; 
		}
		$resultSqlArg = mysqli_query($link,$sqlArg);
		if($resultSqlArg){
			while($rigaSqlArg = mysqli_fetch_assoc($resultSqlArg)){ 
				$args[] = $rigaSqlArg["argomento"];
			}
		}
		$riga['argomenti'] = implode (', ', $args);
		$datiBlog[] = $riga; //salvo dati tutti insieme in array datiBlog
		
	}
	return $datiBlog;

}

function getPostData($idPost){
	//funzione che estrae i dati inerenti all'id post ricevuto come parametro
	global $link;
	//query di estrazione dati post
	$query = "SELECT * FROM post WHERE id = '".addslashes($idPost)."'";
	$result = mysqli_query($link,$query);
	while($riga = mysqli_fetch_assoc($result)){
		$posts = $riga;
	}
	return $posts;
}

if(isset($_GET["search"])){ 
	if(!empty($_GET["search"])){ //se c'è una ricerca
		$ricerca = htmlentities(strip_tags($_GET["search"]));
		if(strlen($ricerca)==1){ //se ricerca è di un carattere
			header("location:javascript://history.go(-1)");
			exit;
		}
		//query di estrazione blog con nome simile alla ricerca
		$sqlBlog = "SELECT * FROM blog WHERE nomeBlog LIKE '%".addslashes($ricerca)."%'";
		$resultBlog = mysqli_query($link, $sqlBlog);
		$blogRaw = [];
		while($rigaBlog = mysqli_fetch_assoc($resultBlog)){
			$blogRaw[] = getBlogData($rigaBlog["id"]);
		}
		$blog = array_unique($blogRaw, SORT_REGULAR); //array dei blog trovati con ricerca per blog
		//query di estrazione blog con nome degli autori simile alla ricerca
		$sqlUtenti = "SELECT * FROM blog WHERE autore LIKE '%".addslashes($ricerca)."%'";
		$resultUtenti = mysqli_query($link, $sqlUtenti);
		$utentiRaw = [];
		while($rigaUtenti = mysqli_fetch_assoc($resultUtenti)){
			$utentiRaw[] = getBlogData($rigaUtenti["id"]); 
		}
		$utenti = array_unique($utentiRaw, SORT_REGULAR); //array dei blog trovati con ricerca per utenti
		$allBlogs = getAllBlogs(); //estrazione di tutti i blog presenti nel db
		$argomentiRaw = [];
		if(!empty($allBlogs[0])){ //se esistono dei blog
			for($i=0;$i<sizeof($allBlogs);$i++){ //scorro i blog
				$argBlog = $allBlogs[$i]["argomenti"]; //salvo in variabile gli argomenti del blog
				if(preg_match("/^(.)*$ricerca(.)*/", $argBlog)){ //se c'è match di argomenti con ricerca 
					$argomentiRaw[] = $allBlogs[$i]; //salvo blog in array
				}
			}
			$argomenti = array_unique($argomentiRaw, SORT_REGULAR); //array dei blog trovati con ricerca per argomenti	
		}
		//query di estrazione post con titolo simile alla ricerca
		$sqlPostTitle = "SELECT * FROM post WHERE titolo LIKE '%".addslashes($ricerca)."%'";
		$resultPT = mysqli_query($link, $sqlPostTitle);
		$postTitlesRaw = [];
		while($rigaPT = mysqli_fetch_assoc($resultPT)){
			$postTitlesRaw[] = getPostData($rigaPT["id"]);
		}
		$postTitles = array_unique($postTitlesRaw, SORT_REGULAR); //array dei post trovati con ricerca per titolo
	} else {
		//se ricerca è vuota
		header("location:javascript://history.go(-1)");
		exit;
	}
	$trovato = false; //set di variabile a false
} else {
	//se ho ricerca avanzata
	if(isset($_POST["usernameSearch"])||isset($_POST["nomeBlogSearch"])){
		if(empty($_POST["usernameSearch"]) && empty($_POST["nomeBlogSearch"])){
			//se accedo a pagina senza parametri
			header("location:javascript://history.go(-1)");
			exit;	
		}
		$ricerca = "";
		if(!empty($_POST["usernameSearch"])){
			$ricerca.= htmlentities(strip_tags($_POST["usernameSearch"]));
		}
		if(!empty($_POST["nomeBlogSearch"])){
			if(!empty($ricerca)){
				$ricerca.= " ".htmlentities(strip_tags($_POST["nomeBlogSearch"]));
			} else {
				$ricerca.= htmlentities(strip_tags($_POST["nomeBlogSearch"]));
			}
		}//query ricerca
		$sqlBlog = "SELECT * FROM blog WHERE nomeBlog LIKE '%".addslashes($_POST["nomeBlogSearch"])."%' AND autore LIKE '%".addslashes($_POST["usernameSearch"])."%'";
		$resultBlog = mysqli_query($link, $sqlBlog);
		$blogRaw = [];
		while($rigaBlog = mysqli_fetch_assoc($resultBlog)){
			$blogRaw[] = getBlogData($rigaBlog["id"]);
		}
		$blog = array_unique($blogRaw, SORT_REGULAR); //array dei blog trovati con ricerca per 
		$trovato = false; //set di variabile a false
	} else {
		//se accedo a pagina senza parametri
		header("location:javascript://history.go(-1)");
		exit;
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
			  <?php
				if (isset($_SESSION["idUtente"])){
					//se utente è loggato
			  ?>
			  <li><a class="links" href="logout.php">Logout</a></li>
			  <li><a class="links" href="mieiBlog.php">I miei Blog</a></li>
			  <li><a class="links" href="modificaAccount.php">Il mio Account </a></li>
			  <?php
				} else {
			  ?> <li><a class="links" href="accesso.php">Accedi</a></li>
			  <?php    
				}
			  ?>
			  <li><a class="links" href="home.php">Home</a></li>
			  <li><form name="searchForm" method="get" action="searchResults.php"><div class="ricerca"><input type="text" id="inputSearchBar" name="search" class="input corti search" placeholder="Cerca Blog, Utenti, Post, .." autocomplete="off" pattern=".{2,}" title="Minimo due caratteri." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Cerca Blog, Utenti, Post, ..'" required><button type="submit" class="button searchButton" id="buttonSearchBar"><i class="fa fa-search"></i></button></div></form></li>
			</ul> 
		</nav>
	</header>
	<body>
		<div class="searchResultsContainer">
			<div class="searchresultsHeader">
				<h2 class="searchResultsTitle">"<?= $ricerca?>"</h2>
				<p class="searchResultsSubtitle">Risultati della ricerca <br>Seleziona un risultato per visualizzarlo</p>
			</div>
			<?php
			if(!empty($blog)){ //se sono stati trovati blog con nome simile a ricerca
				$trovato = true;
			?>
			<h2 class="searchResultsTableTitle">Blog trovati con nome "<?= $ricerca?>"</h2>
			<div class="contenitore" style="width:100%;">
				<table class="tableResults">
					<thead>
						<tr class="shortRows">
							<th>Titolo</th>
							<th>Autore</th>
							<th>Data Creazione</th>
							<th>Temi</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//show dei blog
						for($i=0;$i<sizeof($blog);$i++){ //scorro i blog trovati
							$id = htmlentities($blog[$i]["id"]); //id del blog
							?>
							<tr onclick="window.location.href='blog.php?idBlog=<?=$id?>'" class="shortRows">
								<td>
								<?php   
								echo htmlentities($blog[$i]["nomeBlog"]);
								?>
								</td>
								<td>
								<?php   
								echo htmlentities($blog[$i]["autore"]);
								?>
								</td>
								<td>
								<?php
								$date = htmlentities($blog[$i]["dataCreazione"]);
								//conversione timestamp in data
								$timestamp = strtotime($date);
								echo date('d/m/Y', $timestamp);
								?>
								</td>
								<td>
								<?= htmlentities($blog[$i]["argomenti"])?>
								</td>
								<td>
								<?php
								$descrizione = $blog[$i]["descrizione"];
								if(strlen($descrizione)>30){ //preview di descrizione
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
			}
			if(!empty($utenti)){ //se sono stati trovati blog con nome autore simile a ricerca
				$trovato = true;
			?>
			<h2 class="searchResultsTableTitle">Blog trovati con autore "<?=$ricerca?>"</h2>
			<div class="contenitore" style="width:100%;">
				<table class="tableResults">
					<thead>
						<tr class="shortRows">
							<th>Titolo</th>
							<th>Autore</th>
							<th>Data Creazione</th>
							<th>Temi</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//show di blog
						for($i=0;$i<sizeof($utenti);$i++){ //scorro i blog
							$id = htmlentities($utenti[$i]["id"]); //id del blog
							?>
							<tr onclick="window.location.href='blog.php?idBlog=<?=$id?>'" class="shortRows">
								<td>
								<?php   
								echo htmlentities($utenti[$i]["nomeBlog"]);
								?>
								</td>
								<td>
								<?php   
								echo htmlentities($utenti[$i]["autore"]);
								?>
								</td>
								<td>
								<?php
								$date = htmlentities($utenti[$i]["dataCreazione"]);
								//conversione timestamp in data
								$timestamp = strtotime($date);
								echo date('d/m/Y', $timestamp);
								?>
								</td>
								<td>
								<?= htmlentities($utenti[$i]["argomenti"])?>
								</td>
								<td>
								<?php
								$descrizione = $utenti[$i]["descrizione"];
								if(strlen($descrizione)>30){ //preview di descrizione
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
			}
			if(!empty($argomenti)){ //se sono stati trovati blog con argomenti simile a ricerca
				$trovato = true;
			?>
			<h2 class="searchResultsTableTitle">Blog trovati con temi "<?= $ricerca?>"</h2>
			<div class="contenitore" style="width:100%;">
				<table class="tableResults">
					<thead>
						<tr class="shortRows">
							<th>Titolo</th>
							<th>Autore</th>
							<th>Data Creazione</th>
							<th>Temi</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//show di blog
						for($i=0;$i<sizeof($argomenti);$i++){ //scorro i blog
							$id = htmlentities($argomenti[$i]["id"]); //id del blog
							?>
							<tr onclick="window.location.href='blog.php?idBlog=<?=$id?>'" class="shortRows">
								<td>
								<?php   
								echo htmlentities($argomenti[$i]["nomeBlog"]);
								?>
								</td>
								<td>
								<?php   
								echo htmlentities($argomenti[$i]["autore"]);
								?>
								</td>
								<td>
								<?php
								$date = htmlentities($argomenti[$i]["dataCreazione"]);
								//conversione timestamp in data
								$timestamp = strtotime($date);
								echo date('d/m/Y', $timestamp);
								?>
								</td>
								<td>
								<?= htmlentities($argomenti[$i]["argomenti"])?>
								</td>
								<td>
								<?php
								$descrizione = $argomenti[$i]["descrizione"];
								if(strlen($descrizione)>30){ //preview di descrizione
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
			}
			if(!empty($postTitles)){ //se sono stati trovati post con titolo simile a ricerca
				$trovato = true;
			?>
			<h2 class="searchResultsTableTitle">Post trovati con titolo "<?= $ricerca?>"</h2>
			<div class="contenitore" style="width:100%;">
				<table class="tableResults">
					<thead>
						<tr class="shortRows">
							<th>Titolo</th>
							<th>Autore</th>
							<th>Data e Ora</th>
							<th>Post</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//show di post
						for($i=0;$i<sizeof($postTitles);$i++){ //scorro i post
							$idBlog = htmlentities($postTitles[$i]["idBlog"]); //id del blog
							$idPost = htmlentities($postTitles[$i]["id"]); //id del post
							?>
							<tr onclick="window.location.href='post.php?idBlog=<?=$idBlog?>&idPost=<?=$idPost?>'" class="shortRows">
								<td>
								<?php   
								echo htmlentities($postTitles[$i]["titolo"]);
								?>
								</td>
								<td>
								<?php   
								echo htmlentities($postTitles[$i]["autore"]);
								?>
								</td>
								<td>
								<?php
								$date = htmlentities($postTitles[$i]["dataCreazione"]);
								//conversione timestamp in data
								$timestamp = strtotime($date);
								echo date('d/m/Y', $timestamp);
								?>
								</td>
								<td>
								<?php
								$contenuto = $postTitles[$i]["contenuto"];
								if(strlen($contenuto)>70){ //preview del contenuto
								  echo substr($contenuto, 0, 70). "...";
								} else {
								  echo htmlentities($contenuto);  
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
			}
			if(!$trovato){
				//se non sono stati trovati risultati
				?>
				<h3 class="searchContainerMessage">Non ho trovato nessun risultato con "<?=$ricerca?>"</h3>
			<?php
			}
			?>
		</div>
  	</body>
</html>