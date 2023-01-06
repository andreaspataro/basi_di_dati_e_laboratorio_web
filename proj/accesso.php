<?php
require_once "config.php";
require_once "connect.php";

if(isset($_SESSION["idUtente"])){
	//se utente è già loggato faccio redirect a logout
	header("location:logout.php");
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
		<div class="accedi">
			<form name="form1" method="post" action="login.php" onsubmit="" 
			class=" <?php
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
			//gestione degli alert
			if ((isset($_GET["errore"])) && ($_GET["errore"]=="login")){
				echo "<div class='errorLog'>Attenzione! Nome Utente o Password errati</div>";
			};
			if ((isset($_GET["success"])) && ($_GET["success"]=="accountDeleted")){
				echo "<div class='successReg'>Account eliminato con successo</div>";
			};
			?>
			<h2 class="accediTitle">Accedi</h2>
			<input type="text" name="nome" placeholder="Nome Utente" class="input" style="margin-bottom: 5px;" required><br>
			<input type="password" name="password" placeholder="Password" class="input" required>
			<br><br>
			<input type="submit" value="Accedi" class="button blue">
			</form>
			<hr>
			<h2 class="accediTitle">Non hai un account?</h2>
			<button class="button white" type="button" onclick="window.location.href='registrazione.html'" >Registrati</button>
		</div>
	</body>
</html>