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

if(isset($_GET["idBlog"])){ //ricavo informazioni sul blog
	$id = htmlentities($_GET["idBlog"]); //id del blog
	$query = "SELECT * FROM blog WHERE id = '".addslashes($id)."'"; //ricavo dati sul blog
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
	//estrazione coautori del blog da tabella partecipanti
	$coautori = [];
	$queryCoautori = "SELECT nomeUtente FROM partecipanti WHERE nomeUtente != '".addslashes($datiBlog["autore"])."' AND idBlog = '$id'"; //seleziono partecipanti al blog tranne autore
	$resultCoautori = mysqli_query($link, $queryCoautori);
	while($rigaCoautori = mysqli_fetch_assoc($resultCoautori)){
		$coautori[] = $rigaCoautori["nomeUtente"];
	}
	//conto quanti post ci sono
	$postCount = getresult("SELECT count(id) FROM post WHERE idBlog = '".addslashes($id)."'")["count(id)"];
	//estrazione post del blog da tabella post
	$posts = [];
	$queryPost = "SELECT * FROM post WHERE idBlog = '".addslashes($id)."' ORDER BY dataCreazione DESC LIMIT 3"; //ordinati dal più recente
	$resultPost = mysqli_query($link, $queryPost);
	while($rigaPost = mysqli_fetch_assoc($resultPost)){
		$posts[] = $rigaPost;
	}
	//estrazione del tema e del font del blog
	$style = "SELECT * FROM personalizzazione WHERE idBlog = '".addslashes($id)."'";
	$styleRes = mysqli_query($link,$style);
	$styleRow = mysqli_fetch_assoc($styleRes);
	if(isset($styleRow['temaBlog'])){
		$blogStyle = $styleRow['temaBlog']; //tema del blog
	}
	if(isset($styleRow['fontBlog'])){
		$blogFont = $styleRow['fontBlog']; //font del blog
	}
	if(isset($_SESSION["nomeUtente"])){ 
	//vedo se tu sei propietario del blog
		$username = $_SESSION["nomeUtente"];  
		$queryProp = "SELECT * FROM partecipanti WHERE nomeUtente = '".addslashes($username)."' AND idBlog = '$id'";
		$resultProp = mysqli_query($link,$queryProp);
		$row = mysqli_fetch_assoc($resultProp);
	}
} else {
	header("location:javascript://history.go(-1)");
}

if(!isset($datiBlog)){
	//se blog non esiste 
	header("location:javascript://history.go(-1)");
} 
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<script src="animations.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="https://code.jquery.com/jquery-3.5.1.js"
			integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
			crossorigin="anonymous"></script>
		<meta charset="utf-8">
		<script>
			//script per riempire form in maniera dinamica con dati variabili
			function riempiFormBlog(id){
				//redirect a modificaBlog.php
				document.getElementById("idBlog").value = id;
				document.getElementById("blogForm").submit();
			}
			function riempiFormCreaPost(idBlog){
				//redirect a creaPost.php
				document.getElementById("idBlog1").value = idBlog;
				document.getElementById("creaPostForm").submit();
			}
			function riempiFormModificaPost(idPost){
				//redirect a modificaPost.php
				document.getElementById("idPost").value = idPost;
				document.getElementById("modificaPostForm").submit();
			}
		</script>
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
				<li><a class="links" href="modificaAccount.php">Il mio Account</a></li>
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
	<body class="bodyBlog <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'nero';}if($blogStyle == 'Tema Verde'){echo 'verde';}}if(isset($blogFont)){if($blogFont == "Open Sans"){echo " openSans";}if($blogFont == "Lato"){echo " lato";}if($blogFont == "Noto Sans JP"){echo " noto";}else {echo " roboto";}}?>">

		<div class="all" id="all">
			
			<div class="leftColumn" id="divvone">
				<h1 class="blogTitle"> <?= $datiBlog["nomeBlog"]; ?> <small class="blogSubtitle">creato da <?= htmlentities($datiBlog["autore"]); ?></small></h1>
				
				<div class="postContainer" id="postContainer"></div>
					
					<?php
					//estrazione post
					if(!empty($posts)){
						for($i=0;$i<sizeof($posts);$i++){
							$idPost = $posts[$i]["id"];
							echo '<div class="card ';
							
							if(isset($blogStyle)){
								if($blogStyle == 'Tema Scuro'){
									echo 'neroCards';
								}
								if($blogStyle == 'Tema Verde'){
									echo 'verdeCards';
								}
							}
							echo '">';
							//estrazione immagini del post se esistono
							$postPics = "SELECT directory FROM immagini WHERE idPost = '".addslashes($idPost)."'";
							$picsRes = mysqli_query($link, $postPics);
							$pics = [];
							while($picsRow = mysqli_fetch_assoc($picsRes)){
								$pics[] = htmlentities($picsRow["directory"]);
							}
							if(!empty($pics)){ 
							//se esistono foto per questo post
								echo '<div class="postImage ';
								if(isset($blogStyle)){
									if($blogStyle == 'Tema Scuro'){
										echo 'neroCardsTitle';
									}
									if($blogStyle == 'Tema Verde'){
										echo 'verdeCardsTitle';
									}
								}
								echo '">';
								//prendo solo la prima foto per la preview
								echo '<img src = "'.$pics[0].'" alt="Immagine di preview post" class="dirImage">';  
								echo '</div>';
							}
							echo '<div class="cardBody">';
							echo '<h2 class="postTitle">'.htmlentities($posts[$i]["titolo"]).'</h2>';//titolo
							echo '<p class="postText">';
							$postPreview = htmlentities($posts[$i]["contenuto"]);//contenuto
							if(strlen($postPreview)>70){ //preview del contenuto del post
								$corta = substr($postPreview, 0, 70);
								echo $corta.'...</p>';
							} else {
								echo $postPreview.'</p>';  
							}
							echo '<button class="button white post ';
							if(isset($blogStyle)){
								if($blogStyle == 'Tema Scuro'){
									echo 'neroButtons';
								}
							} 
							echo '"onclick="window.location.href=\'';
							echo 'post.php?idBlog='.htmlentities($id).'&idPost='.htmlentities($idPost).'\'">Continua a Leggere</button>'; //bottone per leggere il post
							if (isset($row)){
								echo '<button class="button grey ';
								if(isset($blogStyle)){
									if($blogStyle == 'Tema Scuro'){
										echo 'neroButtons2';
									}
								}
								echo '"style="float: right;" onclick="riempiFormModificaPost('.htmlentities($idPost).')">Modifica Post</button>'; //bottone per modificare il post se ne hai diritto
							}
							echo '</div>';
							echo '<div class="cardFooter '; 
							if(isset($blogStyle)){
								if($blogStyle == 'Tema Scuro'){
									echo 'neroCardsFooter';
								}
								if($blogStyle == 'Tema Verde'){
									echo 'verdeCardsFooter';
								}
							}
							$date = htmlentities($posts[$i]["dataCreazione"]); //data creazione del post
							$timestamp = strtotime($date);
							$data = date('d/m/Y', $timestamp);
							$ora = date('H:i', $timestamp);
							//dati creazione post
							echo '"> Scritto da '.htmlentities($posts[$i]["autore"]).' il '.$data.' alle '.$ora.'</div>';
							echo '</div>' ;
						}
						if($postCount>3){
							echo '<div id="removeRow">';
							echo '<button class="button blue long ';
							echo '"name="buttonMore" id="buttonMore" data-post="'.addslashes($idPost).'">Mostra post precedenti</button></div>';	
						}
					} else {
						echo '<h3 class="postContainerMessage ';
						if(isset($blogStyle)){
							if($blogStyle == 'Tema Scuro'){
								echo 'nero';
							}
						}
						echo '">Non ci sono ancora post!</h3>';
					}
			?>
			</div>

			<div class="rightColumn">
				<?php
				if (isset($row)){
				?>
				<button class="button blue newPost" type="button" id="buttonCreatePost" onclick="riempiFormCreaPost(<?=htmlentities($id)?>)">+ Crea Nuovo Post</button>
				<?php    
				} else {
				?>
				<div style="height:82px"></div>
				<?php
				}
				?>
				<div class="littleCard <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'neroCards';}if($blogStyle == 'Tema Verde'){echo 'verdeCards';}}?>">
					<div class="littleCardTitle <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'neroCardsTitle';}if($blogStyle == 'Tema Verde'){echo 'verdeCardsTitle';}}?>">About</div>
					<!-- dati blog -->
					<div class="littleCardBody"><?= htmlentities($datiBlog["descrizione"]);?><br><br>
						Creato da: <b>&nbsp;<?= htmlentities($datiBlog["autore"]);?></b><br>
						In data: <b>&nbsp;<?php $date = htmlentities($datiBlog["dataCreazione"]);
								$timestamp = strtotime($date);
								echo date('d/m/Y', $timestamp); ?></b><br>
						Coautori: <b>&nbsp;<?php
						if(empty($coautori)){
							echo "/";
						} else {
							for($i=0; $i<sizeof($coautori);$i++){
								if($i==sizeof($coautori)-1){
									echo htmlentities($coautori[$i]);  
								} else {
									echo htmlentities($coautori[$i]). ", ";
								}
							}  
						}
						?> </b>
					</div>
					<?php
					if (isset($row)){
						//se sei proprietario o coautore del blog
					?>
					<button class="button grey modificaBlog <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'neroButtons2';}}?>" onclick="riempiFormBlog(<?=htmlentities($id)?>)">Modifica Blog</button>
					<?php    
					}
					?>
				</div>
				<div class="littleCard <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'neroCards';}if($blogStyle == 'Tema Verde'){echo 'verdeCards';}}?>">
					<div class="littleCardTitle <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'neroCardsTitle';}if($blogStyle == 'Tema Verde'){echo 'verdeCardsTitle ';}}?>">Argomenti</div>
					<div class="littleCardBody">Argomenti del blog: <br>
						<b>
						<?php
						//argomenti del blog
						$args = explode (", ", htmlentities($datiBlog["argomenti"]));
						foreach ($args as &$arg) {
							$arg = "<a href='searchResults.php?search=".$arg."'>$arg</a>";
						}
						echo implode(" , ", $args);
						?>
						</b>
					</div>
				</div>
			</div>
		</div>
		<form name="blogForm" id="blogForm" method="post" action="modificaBlog.php">
			<input type="hidden" id="idBlog" name="idBlog" value="">
		</form>
		<form name="creaPostForm" id="creaPostForm" method="post" action="creaPost.php">
			<input type="hidden" id="idBlog1" name="idBlog" value="">
		</form>
		<form name="modificaPostForm" id="modificaPostForm" method="post" action="modificaPost.php">
			<input type="hidden" id="idPost" name="idPost" value="">
		</form>
	</body>
	<footer id="footer">
		<p>I contenuti presenti sul blog "<?= htmlentities($datiBlog["nomeBlog"]); ?>" sono di proprietà di "<?= htmlentities($datiBlog["nomeBlog"]);?>" o dell'autore "<?= htmlentities($datiBlog["autore"]); ?>" </p>
	</footer>
</html>
<script>
$(document).ready(function(){
	$(document).on('click', '#buttonMore', function(){
		var lastPostId = $(this).data("post");
		$('#buttonMore').html("Carico...");
		$.ajax({
			url:"fetchPost.php",
			method:"POST",
			data:{lastPostId:lastPostId, idBlog:<?=addslashes($id)?>},
			dataType:"text",

			success: function(data){
				console.log(data);
				if(data != ''){
					$("#removeRow").remove();
					$("#divvone").append(data);
				} else {
					$("#buttonMore").html("Sono stati caricati tutti i post");
				}
			}
		});
	});
});
</script>