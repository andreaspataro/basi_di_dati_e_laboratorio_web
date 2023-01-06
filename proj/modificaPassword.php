<?php
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<script src="animations.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<meta charset="utf-8">
	</head>
	<header>
		<nav>
			<ul>
				<li><a class="links" href="logout.php">Logout</a></li>
				<li><a class="links" href="mieiBlog.php">I miei Blog</a></li>
				<li><a class="links" href="modificaAccount.php">Il mio Account</a></li>
				<li><a class="links" href="home.php">Home</a></li>
				<li><form name="searchForm" method="get" action="searchResults.php"><div class="ricerca"><input type="text" id="inputSearchBar" name="search" class="input corti search" placeholder="Cerca Blog, Utenti, Post, .." autocomplete="off" pattern=".{2,}" title="Minimo due caratteri." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Cerca Blog, Utenti, Post, ..'" required><button type="submit" class="button searchButton" id="buttonSearchBar"><i class="fa fa-search"></i></button></div></form></li>
			</ul> 
		</nav>
	</header>
	<body>
		<div class="all" id="all">
			<div class="superContainer">
				<div>
					<h2 style='font-size:40px;margin-bottom: 0px;'>Modifica Password</h2>
					<p class="sottotitolo">Qui sotto sono mostrati i dati del tuo account<br>Seleziona una box per modificarla</p>
					<div class="formContainer">
						<form name="form2" method="post" action="doModificaPassword.php" 
								class="<?php 
								if(isset($_GET["errore"])){
									echo "error";	
								} else {
									echo "";
								};
								if(isset($_GET["success"])){
									echo "success";
								} else {
									echo "";
								}; 
								?>">
							<?php 
							//gestione alert
							if(isset($_GET["errore"])) {
								if($_GET["errore"]=="campi") {
									echo "<div class='errorReg'>Attenzione! È necessario riempire tutti i campi</div>";	
								};
								if($_GET["errore"]=="inputLength"){
									echo "<div class='errorReg'>Attenzione! Uno o più dati inseriti superano i limiti di lunghezza<br> Per favore ricontrolla i tuoi dati</div>";
								};
								if($_GET["errore"]=="oldPass"){
									echo "<div class='errorReg'>Attenzione! La vecchia password è errata<br> Per favore ricontrolla i tuoi dati</div>";
								};
								if($_GET["errore"]=="confirm"){
									echo "<div class='errorReg'>Attenzione! Password e conferma non corrispondono<br> Per favore ricontrolla i tuoi dati</div>";
								};
							};
							?>
							<h3 style="margin-top: 0;">Credenziali</h3>
							<label for=”oldPassword”>Vecchia password</label>
							<input type="password" autocomplete="off" name="oldPassword" class="input lunghi" maxlength="128" placeholder="Inserisci vecchia Password"><br><br>
							<label for=”newPassword”>Nuova password</label>
							<input type="password" autocomplete="off" name="newPassword" class="input lunghi" maxlength="128" placeholder="Inserisci nuova Password"><br><br>
							<label for=”confirm”>Conferma password</label>
							<input type="password" autocomplete="off" name="confirm" class="input lunghi" maxlength="128" placeholder="Conferma Password"><br><br>
							<input type="submit" value="Modifica Password" class="button white signIn">
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>