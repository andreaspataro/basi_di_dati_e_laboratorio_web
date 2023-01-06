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

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato non può accedere alla pagina
	header("location:accesso.php");
	exit;
}

if(isset($_POST["idBlog"])){ //ricavo informazioni sul blog
	$idBlog = htmlentities($_POST["idBlog"]); //id del blog
	//query di controllo se utente è proprietario o coautore del blog
	$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($_SESSION["nomeUtente"])."'";
	$controlloRes = getresult($controllo);
	if(empty($controlloRes)){
		//se non sei proprietario del blog
		header("location:javascript://history.go(-1)"); 
		exit;
	}
} else {
	//se manca id del blog
	header("location:javascript://history.go(-1)"); 
	exit;
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
		<div class="modificaPost">
			<h1 style="text-align: left;margin-bottom: -5px;">Crea un Nuovo Post</h1>
			<hr class="postDivider"> 
			<form enctype="multipart/form-data" name="formPost" method="post" action="doCreaPost.php"
				class="<?php 
					if(isset($_POST["errore"])){
						echo "error"; 
					} else {
						echo "";
					};
					?>">
					<?php 
					//gestione alert
					if(isset($_POST["errore"])) {
						if($_POST["errore"]=="campi") {
							echo "<div class='errorReg'>Attenzione! È necessario riempire tutti i campi</div>"; 
						}
						if($_POST["errore"]=="inputLengthTitle"){
							echo "<div class='errorReg'>Attenzione! Il titolo inserito supera i limiti di lunghezza<br> Per favore riduci la lunghezza</div>"; 
						}
						if($_POST["errore"]=="inputLengthText"){
							echo "<div class='errorReg'>Attenzione! Il contenuto del post supera i limiti di lunghezza<br> Per favore riduci la lunghezza</div>"; 
						}
						if($_POST["errore"]=="fileNum"){
							echo "<div class='errorReg'>Attenzione! Puoi aggiungere al massimo due immagini per post</div>"; 
						}
						if($_POST["errore"]=="formato"){
							echo "<div class='errorReg'>Attenzione! Puoi aggiungere solo immagini con formato '.jpg, .jpeg, .png, .jfif '</div>"; 
						}
						if($_POST["errore"]=="size"){
							echo "<div class='errorReg'>Attenzione! Ogni immagine può essere grande al massimo 500kb</div>"; 
						}
					}
					?>
				<input type="text" name="titolo" placeholder="Titolo del Post" class="input creaPost" id="postTitle" maxlength="128" required> 
				<textarea name="post" class="newPostText input" placeholder="Scrivi qualcosa..." id="newPostText" maxlength="20000" required></textarea>
				<p class="sottotitolo"></p>
				<input type="hidden" name="idBlog" value="<?=$idBlog?>">
				<input type="file" name="pic[]" id="file" multiple accept='.jpg, .jpeg, .png, .jfif' onchange="photoUpload()">
				<label for="file" class="inserisciImmagini" id="labelPhoto">Aggiungi immagini. Puoi aggiungere al massimo due immagini per post</label>
				<input type="submit" class="button white" style="margin: 0;float: right;" value="Pubblica">
				<button class="button grey" type="button" style="margin: 0 10px 0 0;float: right;"id="annullaButton" onclick="window.history.back();">Annulla</button>
			</form>
		</div>
		<br>
		<br>
	</body>
</html>