<?php
require_once "config.php";
require_once "connect.php";

if (isset($_SESSION["idUtente"])){
	//se utente è loggato procedo con logout
	unset($_SESSION["idUtente"]);
	unset($_SESSION["nomeUtente"]);
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<meta charset="utf-8">
	</head>
	<header>
		<nav>
			<ul>
				<li><a class="links" href="home.php">Home</a></li>
			</ul> 
		</nav>
	</header>
	<body>
		<?php
		if(isset($_GET["selezione"]) && $_GET["selezione"]=="premium"){
			//se selezione è stata account premium
			echo "<h2 style='font-size:40px;margin-bottom: 10px;'>Account Premium</h2>";
		} else {
			//se selezione è stata account gratis
			echo "<h2 style='font-size:40px;margin-bottom: 10px;'>Account Gratis</h2>";	
		};
		?>
		<div class="formContainer">
			<form name="form2" method="post" action="doRegistrazione.php" 
					class="<?php 
					if(isset($_GET["errore"])){
						echo "error";	
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
					if(($_GET["errore"]=="val")&&(isset($_GET["valType"]))) {
						echo "<div class='errorReg'>Attenzione! ' ".htmlentities($_GET["valType"])." ' non rispetta il formato standard<br> Per favore ricontrolla i tuoi dati</div>";	
					};
				};
				?>
				<h3 style="margin-top: 0;">Credenziali</h3>
				<section>
					<div style="float:left;;width:50%;">
						<label for="username">Username</label>
						<input type="text" name="username" pattern="[a-zA-Z0-9]*([._]?[a-zA-Z0-9]+)*" maxlength="30" id="username" class="input corti" placeholder="es. mario.rossi, mario_rossi" required><br><br>
					</div>
					<div style="float:left;width:50%;">
						<label for=”password” style="margin-left:29px;">Password</label>
						<input type="password" name="password" class="input corti" maxlength="128" style="margin-left:29px;" required><br><br>
					</div>
					<br style="clear:both;" />
				</section>
				<label for="email">Email</label>
				<input type="email" name="email" class="input lunghi" maxlength="128" placeholder="es. mail@esempio.com" required><br><br>
				<label for=”telefono”>Telefono</label>
				<input type="tel" pattern="[0-9]*" name="telefono" maxlength="15" class="input lunghi" placeholder="es. 0000000000" required><br><br>
				<label for="documento">Estremi Documento d'Identità</label>
				<input type="text" name="documento" pattern="[a-zA-Z]{2}[0-9]{5}[a-zA-Z]{2}" maxlength="9" class="input lunghi" placeholder="es. CA00000AA" required>
				<?php
					if(isset($_GET["selezione"]) && $_GET["selezione"]=="premium"){
						//se selezione è stata account premium
						echo "<hr class='divider'>
									<h3>Carta di Credito</h3>
									<input type='hidden' name='selection' value='premium'>
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
										<input type='date' name='scadenza' max='9999-12-31' class='input corti' required>
									</div>
									<div style='float:left;;width:50%;'>
										<label for='CVV' style='margin-left:29px;'>CVV</label>
										<input type='text' pattern= '[0-9]{3}' maxlength='3' name='CVV' class='input corti' style='margin-left:29px;' placeholder='es. 000' required>
									</div>";
					};
				?>
				<input type="submit" value="Registrati" class="button white signIn">
			</form>
		</div>
	</body>
</html>