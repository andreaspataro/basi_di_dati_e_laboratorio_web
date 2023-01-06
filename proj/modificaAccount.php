<?php
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

$id = $_SESSION['idUtente']; //id utente

//query di controllo se utente è premium o gratis in tabella utente
$sql = "SELECT numCartaCredito FROM utente WHERE id = '".addslashes($id)."'";
$result = mysqli_query($link,$sql);
$riga = mysqli_fetch_assoc($result);

//query di estrazione dati account utente da tabella utente
$sql2 = "SELECT * FROM utente WHERE id = '".addslashes($id)."'";
$result2 = mysqli_query($link,$sql2);
$riga2 = mysqli_fetch_assoc($result2);

if(isset($riga2)){ //se utente esiste salvo dati
	$nomeUtente = $riga2['nomeUtente']; 
	$email = $riga2['email'];
	$telefono = $riga2['telefono'];
	$estremiDocumento = $riga2['estremiDocumento'];
	$numCartaCredito = $riga2['numCartaCredito'];
	$nomeCartaCredito = $riga2['nomeCartaCredito'];
	$scadenzaCartaCredito = $riga2['scadenzaCartaCredito'];
	$CVVCartaCredito = $riga2['CVVCartaCredito'];
} else { //se utente non esiste
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
		<meta charset="utf-8">
	</head>
	<header>
		<nav>
			<ul>
				<li><a class="links" href="logout.php">Logout</a></li>
				<li><a class="links" href="mieiBlog.php">I miei Blog</a></li>
				<li><a class="links" href="home.php">Home</a></li>
				<li><form name="searchForm" method="get" action="searchResults.php"><div class="ricerca"><input type="text" id="inputSearchBar" name="search" class="input corti search" placeholder="Cerca Blog, Utenti, Post, .." autocomplete="off" pattern=".{2,}" title="Minimo due caratteri." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Cerca Blog, Utenti, Post, ..'" required><button type="submit" class="button searchButton" id="buttonSearchBar"><i class="fa fa-search"></i></button></div></form></li>
			</ul> 
		</nav>
  	</header>
	<body>
		<div class="littleCard delete" id="deleteAccount">
			<div class="littleCardTitle">Sei sicuro?</div>
			<div class="littleCardBody">
				Il tuo account verrà eliminato in maniera permanente. <br>
			  	<button class="button grey" onclick="closeDeleteAccount()">Annulla</button>
				<button class="button red" onclick="window.location='deleteAccount.php'">Elimina</button>
			</div>
		</div>
		<div class="all" id="all">
			<div class="superContainer">
				<div>
					<h2 style='font-size:40px;margin-bottom: 0px;'>Il Mio Account</h2>
					<p class="sottotitolo">Qui sotto sono mostrati i dati del tuo account<br>Seleziona una box per modificarla</p>
					<div class="formContainer">

						<form name="form2" method="post" action="updateAccount.php" 
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
								if($_GET["errore"]=="username"){
									echo "<div class='errorReg'>Attenzione! Username già in uso</div>";	
								};
								if($_GET["errore"]=="inputLength"){
									echo "<div class='errorReg'>Attenzione! Uno o più dati inseriti superano i limiti di lunghezza<br> Per favore ricontrolla i tuoi dati</div>";
								};
								if(($_GET["errore"]=="val") && (isset($_GET["valType"]))) {
									echo "<div class='errorReg'>Attenzione! ".htmlentities($_GET["valType"])." non rispetta il formato standard<br> Per favore ricontrolla i tuoi dati</div>";	
								};
							};
							if(isset($_GET["success"])) {
								if($_GET["success"]==1) {
									echo "<div class='successReg'>Modifiche dell'account andate a buon fine</div>";
								};
								if($_GET["success"]=="premium"){
									echo "<div class='successReg'>Passaggio a premium completato</div>";
								};
							};
							?>
							<h3 style="margin-top: 0;">Credenziali</h3>
							<label for="username">Username</label>
							<input type="text" name="username" pattern="[a-zA-Z0-9]*([._]?[a-zA-Z0-9]+)*" maxlength="30" id="username" value= "<?php echo htmlentities($nomeUtente); ?>" class="input lunghi" required><br><br>
							<label for="email">Email</label>
							<input type="email" name="email" maxlength="128" class="input lunghi" value= "<?php echo htmlentities($email); ?>" required><br><br>
							<label for=”telefono”>Telefono</label>
							<input type="tel" pattern="[0-9]*" name="telefono" maxlength="15" class="input lunghi" value= "<?php echo htmlentities($telefono); ?>" required><br><br>
							<label for="documento">Estremi Documento d'Identità</label>
							<input type="text" name="documento" pattern="[a-zA-Z]{2}[0-9]{5}[a-zA-Z]{2}" maxlength="9" class="input lunghi" value= "<?php echo htmlentities($estremiDocumento); ?>" required>
							<?php
							if($riga["numCartaCredito"]){ //se utente è premium
							echo '<hr class="divider">
							<h3>Carta di Credito</h3>
							<input type="hidden" name="selection" value="premium">
							<div style="float:left;width:50%;">
								<label for="carta">Numero Carta di Credito</label>
								<input type="text" name="carta" pattern= "[0-9]*" maxlength="16" class="input corti" value = "'.htmlentities($numCartaCredito).'" required><br><br>
							</div>
							<div style="float:left;width:50%;">
								<label for="nominativo" style="margin-left:29px;">Nome</label>
								<input type="text" name="nominativo" pattern="[a-zA-Z]*(\s?[a-zA-Z]+)*" maxlength="128" class="input corti" value = "'.htmlentities($nomeCartaCredito).'" style="margin-left:29px;" required><br><br>
							</div>
							<div style="float:left;width:50%;margin-bottom:0px;">
								<label for="scadenza">Scadenza</label>
								<input type="date" name="scadenza" max="9999-12-31" class="input corti" value = "'.htmlentities($scadenzaCartaCredito).'" required>
							</div>
							<div style="float:left;width:50%;">
								<label for="CVV" style="margin-left:29px;">CVV</label>
								<input type="text" name="CVV" pattern= "[0-9]{3}" maxlength="3" class="input corti" style="margin-left:29px;" value = "'.htmlentities($CVVCartaCredito).'" required>
							</div>';
							};?>
							<br>
							<input type="submit" value="Modifica" class="button white signIn">
						</form>
					</div>
				</div>
				<div>
					<div class="littleCard Mod">
						<div class="littleCardTitle">Altre Opzioni</div>
						<div class="littleCardBody" >
							<button class='button grey' onclick='window.location.href="modificaPassword.php"'>Modifica Password</button>
							<?php
							if(!$riga["numCartaCredito"]){ //se utente non è premium
							?>
							
							<button class='button grey' onclick='window.location.href="premiumAccount.php"'>Passa a Premium</button>
							<?php
							};
							?>
							<button class="button red" onclick="deleteAccount()">Elimina Account</button>
						</div>
			  		</div>
				</div>
			</div>
		</div>
	</body>
</html>