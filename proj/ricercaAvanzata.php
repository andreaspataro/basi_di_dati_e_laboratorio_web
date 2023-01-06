<?php
require_once "config.php";
require_once "connect.php";

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
		<div class="all" id="all">
			<div class="superContainer">
				<div>
					<h2 style='font-size:40px;margin-bottom: 0px;'>Ricerca Avanzata</h2>
					<p class="sottotitolo">Inserisci nelle box i criteri della tua ricerca avanzata<br>Puoi trovare un blog cercando il nome del blog e il suo autore</p>
					<div class="formContainer">
						<form name="form2" method="post" action="searchResults.php">
							<h3 style="margin-top: 0;">Termini della ricerca</h3>
							<label for="username">Username</label>
							<input type="text" name="usernameSearch" class="input lunghi"><br><br>
							<label for="nomeBlog">Nome Blog</label>
							<input type="text" name="nomeBlogSearch" class="input lunghi">
							<br>
							<input type="submit" value="Cerca" class="button white signIn">
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>