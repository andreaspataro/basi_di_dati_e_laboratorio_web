<?php
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//dati
$userId = $_SESSION['idUtente']; //id utente

//query di controllo se utente ha account premium o gratis
$sql = "SELECT numCartaCredito FROM utente WHERE id = '".addslashes($userId)."'";
$result = mysqli_query($link,$sql);
$riga = mysqli_fetch_assoc($result);

if(!empty($riga["numCartaCredito"])){ 
	//se utente ha già account premium
	header("location:javascript://history.go(-1)");
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<script src="animations.js"></script>
		<meta charset="utf-8">
	</head>
	<header>
		<nav>
			<ul>
				<li><a class="links" href="logout.php">Logout</a></li>
				<li><a class="links" href="mieiBlog.php">I miei Blog</a></li>
				<li><a class="links" href="modificaAccount.php">Il mio Account</a></li>
				<li><a class="links" href="home.php">Home</a></li>
			</ul>
		</nav>
	</header>
	<body>
		<h2 style='font-size:40px;margin-bottom: 0px;margin-top: 50px;'>Passa a Premium</h2>
		<p class="sottotitolo">Inserisci i dati richiesti per ricevere i vantaggi dell'account premium</p>
		<div class="formContainer">
			<form name="form2" method="post" action="doPremium.php" 
					class="<?php 
					if(isset($_GET["errore"])){
						echo "error";	
					} else {
						echo "";
					}
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
					if($_GET["errore"]=="numBlog"){
						echo "<div class='errorReg'>Attenzione! Devi avere un account Premium per creare più di 2 blog<br> Passa a Premium inserendo i tuoi dati</div>";	
					};
					if(($_GET["errore"]=="val") && (isset($_GET["valType"]))) {
						echo "<div class='errorReg'>Attenzione! ".htmlentities($_GET["valType"])." non rispetta il formato standard<br> Per favore ricontrolla i tuoi dati</div>";	
					};
				};
				?>
				<h3>Carta di Credito</h3>
				<div style='float:left;;width:50%;'>
					<label for='carta'>Numero Carta di Credito</label>
					<input type='text' pattern= '[0-9]*' maxlength='16' name='carta' class='input corti' placeholder='es. 0000000000000000' required><br><br>
				</div>
				<div style='float:left;;width:50%;'>
					<label for='nominativo' style='margin-left:29px;'>Nome</label>
					<input type='text' pattern='[a-zA-Z]*(\s?[a-zA-Z]+)*' maxlength='128' name='nominativo' class='input corti' style='margin-left:29px;' placeholder='es. Mario Rossi' required><br><br>
				</div>
				<div style='float:left;;width:50%;'>
					<label for='scadenza'>Scadenza</label>
					<input type='date' name='scadenza' max='9999-12-31' class='input corti' required><br><br>
				</div>
				<div style='float:left;;width:50%;'>
					<label for='CVV' style='margin-left:29px;'>CVV</label>
					<input type='text' pattern= '[0-9]{3}' maxlength='3' name='CVV' class='input corti' style='margin-left:29px;' placeholder='es. 000' required><br><br>
				</div>
				<input type="submit" value="Conferma" class="button white signIn">
			</form>
		</div>
	</body>
</html>