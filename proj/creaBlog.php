<?php 
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato non può accedere alla pagina
	header("location:accesso.php");
	exit;
}

//dati
$userId = $_SESSION['idUtente']; //id utente 
$username = $_SESSION['nomeUtente']; //nome utente

//query di controllo se utente che sta creando il blog è utente premium o gratis
$sql = "SELECT numCartaCredito FROM utente WHERE id = '".addslashes($userId)."'";
$result = mysqli_query($link,$sql);
$riga = mysqli_fetch_assoc($result);

//query di controllo per il numero di blog creati da utente
$sql2 = "SELECT id FROM blog WHERE autore = '".addslashes($username)."'";
$result2 = mysqli_query($link,$sql2);
$numeroBlog = [];
while($riga2 = mysqli_fetch_assoc($result2)){
	$numeroBlog[] = $riga2["id"];
}

if(sizeof($numeroBlog)>=2 && !isset($riga["numCartaCredito"])){
	//se utente è gratis e ha creato più di 2 blog redirect a passa a premium
	header("location:premiumAccount.php?errore=numBlog");
}

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
		<script src="animations.js"></script>
		<meta charset="utf-8">
		<script>
			/*script creato attraverso le dispense con aggiunte dalla pagina web di jquery*/
			//autocomplete del campo coautori della form
			$(function(){
			function split( val ) {
				//funzione che fa lo split sugli spazi
				return val.split( /,\s*/ );
			}

			function extractLast( term ) {
				//funzione che ritorna l'ultimo elemento dopo lo split
				return split( term ).pop();
			}

			$("#inputCoautori").keyup(function(k){
				//evento keyup nell'input
				 var term = extractLast($(this).val()); //ricavo l'ultima cosa scritta
				 $.ajax({ //chiamata ajax alla pagina fetch.php
						type: "POST",
						url: "fetch.php",
						data: 'keyword='+term,
						dataType: "json",
						
						success: function(data){
							console.log(data);
							$('.autocompleteContainer div').empty();
							if(data.length == 0 && extractLast($("#inputCoautori").val()).length>0){
								//se la chiamata ajax non ha ritornato niente 
								$(".autofill").show();
								$('.autocompleteContainer div').append("<p class='rigaElenco click'>Utente non trovato</p>");
							} else {
								//se la chiamata ajax ha ritornato qualcosa
								$(".autofill").show();
								for(var i = 0;i<data.length;i++){
									//scorro i risultati e li appendo
									$('.autocompleteContainer div').append("<p class='rigaElenco click'>"+data[i]+"</p>");
								}
								
								$(".click").click(function(){
									//se un risultato nella tendina viene cliccato inserisco il risultato cliccato nell'input seguito da una virgola
									var word = $(this).text(); //prendo il testo del risultato cliccato
									/*elimino l'ultima cosa scritta per sostituirla con il risultato cliccato
									es. adm viene sostituito con admin, */
									var inputVal = split($("#inputCoautori").val()); 
									inputVal.pop(); //elimino l'ultima cosa scritta
									inputVal.push(word); //inserisco il risultato cliccato
									$("#inputCoautori").val(inputVal.join(", ")); //inserisco il risultato nell'input con virgola e spazio
									$(".autofill").hide(); //nascondo la tendina
									$("#inputCoautori").focus(); //lascio il focus sull'input
								})
							}
						}
					});         
				})
			})
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
	<body>
		<h2 class="titles">Crea Nuovo Blog</h2>
		<p class="sottotitolo">Inserisci i dati per la formazione del blog. <br>Potrai modificarli anche successivamente alla creazione del blog.</p>
		<div class="formContainer">
			<form name="form" method="post" action="doCreaBlog.php" 
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
						}
						if($_GET["errore"]=="inputLength"){
							echo "<div class='errorReg'>Attenzione! Uno o più dati inseriti superano i limiti di lunghezza<br> Per favore ricontrolla i tuoi dati</div>"; 
						}
						if($_GET["errore"]=="hack"){
							echo "<div class='errorReg'>Attenzione! Hai tentato di manomettere la form <br>Utilizza correttamente i dati che ti vengono forniti</div>"; 
						}
						if(($_GET["errore"]=="invalid")&&(isset($_GET["invalidType"]))) {
							echo "<div class='errorReg'>Attenzione! Argomento: ' ".htmlentities($_GET["invalidType"])." ' non rispetta il formato standard<br> Per favore ricontrolla i tuoi dati</div>";  
						};
						if(($_GET["errore"]=="coautore")&&(isset($_GET["valType"]))) {
							echo "<div class='errorReg'>Attenzione! Coautore: ' ".htmlentities($_GET["valType"])." ' non esiste o è il tuo nome utente<br> Per favore ricontrolla i tuoi dati</div>"; 
						};
					}
					?>
				<br>
				<label for=”titolo”>Titolo</label>
				<input type="text" name="titolo" maxlength="60" class="input lunghi" placeholder="Nome del Blog" required><br><br>
				<label for=”coautore”>Coautori</label>
				<div id="divCoautori">
					<input type="text" name="coautori" autocomplete="off" class="input lunghi" id="inputCoautori" placeholder="Inserisci i Coautori del Blog separati da una virgola ','">
					<div class="autocompleteContainer autofill">
						<div></div>
					</div>
				</div>
				<h3 class="creaBlogTitles">Argomenti</h3>
				<label for=”categoria”>Argomenti del Blog</label>
				<div id="divTemi">
					<input type="text" name="argomenti[]" class="input lunghi" id="here" placeholder="Inserisci Argomento e Sottoargomento (opzionale) separati da una virgola ','" pattern="[a-zA-Z0-9]+(,[a-zA-Z0-9]+)*" maxlength="128" required>
				</div>
				<div class="containerButtons">
					<button type="button" class="button white sottotema" onclick="aggiungiTema();">+ Argomento</button>
					<button type="button" id="removeTemi" class="button remove sottotema" onclick="rimuoviTema();">- Rimuovi</button>
				</div>
				<label for=”descrizione”>Breve Descrizione del Blog</label>
				<textarea class="descrizioneBlog" name="descrizione" maxlength="120" placeholder="Scrivi qualcosa sul blog che stai creando" required></textarea>
				<?php
				if($riga["numCartaCredito"]){ //se utente è premium
					echo '
				<input type="hidden" name="selection" value="premium">
				<h3 class="creaBlogTitles">Personalizzazione</h3>
				<div style="float:left;;width:50%;">
					<label for=”temaBlog”>Scegli il Tema</label>
					<select name="temaBlog" class="input corti select" style="width: 90%;" required><br><br>
						<option class="selectOption">Tema Default</option>
						<option class="selectOption">Tema Scuro</option>
						<option class="selectOption">Tema Verde</option>
					</select>
				</div>
				<div style="float:right;;width:50%;">
					<label for=”font” style="margin-left: 10%">Scegli il Font</label>
					<select name="font" class="input corti select" style="width: 90%; margin-left: 10%;" required><br><br>
						<option class="selectOption roboto">Roboto (default)</option>
						<option class="selectOption openSans">Open Sans</option>
						<option class="selectOption lato">Lato</option>
						<option class="selectOption noto">Noto Sans JP</option>
					</select>
				</div>
				<br style="clear:both;"/>';
				};
				?> 
				<input type="submit" value="Crea Blog" class="button blue signIn">
			</form>
		</div>
	</body>
</html>