<?php 
require_once "config.php";
require_once "connect.php";

//query di estrazione ultimi 6 post
$sql = "SELECT * FROM post ORDER BY dataCreazione DESC LIMIT 6";
$result = mysqli_query($link, $sql);
$post = [];
while($sqlRow = mysqli_fetch_assoc($result)){
	$post[] = $sqlRow;
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
			<h2 class="home">Home</h2>
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
			</ul> 
		</nav>
	</header>
	<body>
		<form name="searchForm" method="get" action="searchResults.php">
			<div class="bar">
				<input class="searchbar" id="inputSearchBar" autocomplete="off" pattern=".{2,}" title="Minimo due caratteri." name="search" type="text" placeholder="Cerca Blog, Utenti, Post, Argomenti, ..." required>
			</div>
			<div class="buttons">
				<input type="submit" class="button grey" id="buttonSearchBar" value="Cerca">
				<button class="button grey" type="button" onclick="window.location.href='ricercaAvanzata.php'">Ricerca Avanzata</button>
				<button class="button white" type="button" onclick="window.location.href='creaBlog.php'">+ Crea Nuovo Blog</button>
			 </div>
		</form>
		<div class="recentPosts">
			<h2 style="font-size: 40px; margin-bottom: 10px;">Post Recenti</h2>
			<div class="content">
				<table>
					<thead>
					 <tr class="tableTitle">
						 <th>Titolo</th>
						 <th>Autore</th>
						 <th>Data e Ora</th>
						 <th>Post</th>
					 </tr>
					</thead>
					<tbody>
						<?php
						//show dei 6 post più recenti
						if(!empty($post)){
							for($i=0;$i<sizeof($post);$i++){
								$idPost = htmlentities($post[$i]["id"]);
								$idBlog = htmlentities($post[$i]["idBlog"]);
								?> 
								<tr class="trHighlight">
								<td>
								<?php   
								echo htmlentities($post[$i]["titolo"]);
								?>
								</td>
								<td>
								<?php   
								echo htmlentities($post[$i]["autore"]);
								?>
								</td>
								<td>
								<?php
								$date = htmlentities($post[$i]["dataCreazione"]);
								$timestamp = strtotime($date);
								echo date('d/m/Y H:i', $timestamp);
								?>
								</td>
								<td>
								<?php
								$contenuto = htmlentities($post[$i]["contenuto"]); 
								if(strlen($contenuto)>30){
									//preview del contenuto
									echo substr($contenuto, 0, 30). "... <a href='post.php?idBlog=$idBlog&idPost=$idPost'>visualizza</a>";
								} else {
									echo $contenuto. "... <a href='post.php?idBlog=$idBlog&idPost=$idPost'>visualizza</a>";  
								}
								?>
								</td>
								</tr>
							<?php
							}
						}
						?>
					</tbody>
				</table> 
			</div>
		</div>    
	</body>
</html>