<?php
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

function getresult($sql){
	//funzione per estrarre risultato query
	global $link;
	$r = mysqli_query($link, $sql);
	if ($r){
		return mysqli_fetch_assoc($r);
	}
	return false;
}

if(isset($_POST['idBlog'])){ //se c'è id blog
	$blogId = htmlentities($_POST['idBlog']); //id blog
	//query di controllo se utente è proprietario o coautore del blog
	$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($blogId)."' AND nomeUtente = '".addslashes($_SESSION["nomeUtente"])."'";
	$controlloRes = getresult($controllo);
	if(empty($controlloRes)){ //se utente non è proprietario o coautore
		header("location:javascript://history.go(-1)"); 
		exit;
	}
	//query di controllo se blog è stato creato da utente premium o gratis
	$sql = "SELECT autore FROM blog WHERE id = '".addslashes($blogId)."'"; 
	$userPremium = getresult($sql)["autore"];
	$premium = getresult("SELECT numCartaCredito FROM utente WHERE nomeUtente = '".addslashes($userPremium)."'")["numCartaCredito"];
	//query di estrazione tema e font del blog
	$style = getresult("SELECT * FROM personalizzazione WHERE idBlog = '".addslashes($blogId)."'");
	$listaTemi = ['Tema Default','Tema Scuro','Tema Verde']; //temi consentiti
	$listaFont = ['Roboto (default)','Open Sans','Lato','Noto Sans JP']; //font consentiti

	//ricavo dati sul blog
	$sql2 = "SELECT * FROM blog WHERE id = '".addslashes($blogId)."'";
	$result = mysqli_query($link, $sql2);
	while($riga = mysqli_fetch_assoc($result)){
		//estrazione argomenti e sottoargomenti del blog da tabelle argomenti e argomentiblog
		//ricavo i sottoargomenti (idParent != 0)
		$querySub = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($blogId)."' AND argomenti.idParent != 0";
		$resultSub = mysqli_query($link,$querySub);
		$args = [];
		$subs = [];
		while($rigaSub = mysqli_fetch_assoc($resultSub)){ 
			$subs[] = $rigaSub["idParent"];
			//sottoargomento ha per forza argomento
			//ricavo gli argomenti per i sottoargomenti estratti (idParent = 0)
			$queryArg = "SELECT DISTINCT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($blogId)."' AND argomenti.idParent = 0 AND argomenti.id = '".$rigaSub["idParent"]."'";
			$resultArg = mysqli_query($link,$queryArg);
			while($rigaArg = mysqli_fetch_assoc($resultArg)){ 
				$args[] = $rigaArg['argomento'].",".$rigaSub['argomento']; //salvo argomento e sottoargomento estratto in variabile
			}
		}
		//estraggo argomenti senza sottoargomento
		//se id dell'argomento non è contenuto nell'array sottoargomenti come idParent di qualche sottoargomento vuol dire che è un argomento senza sottoargomenti
		$sqlArg = "SELECT argomenti.id, argomenti.idParent, argomenti.argomento, argomentiblog.idBlog FROM argomenti, argomentiblog WHERE argomentiblog.idArgomenti = argomenti.id AND argomentiblog.idBlog = '".addslashes($blogId)."' AND argomenti.idParent = 0 ";
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
		$infoBlog = $riga; //salvo dati tutti insieme in array infoBlog
	}

	if(!isset($infoBlog)){
		//se blog non esiste
		header("location:javascript://history.go(-1)"); 
		exit;
	}

	$nomeBlog = $infoBlog["nomeBlog"];

	$coautori = [];
	//query di estrazione dei coautori del blog da tabella partecipanti
	$sql3 = "SELECT nomeUtente FROM partecipanti WHERE nomeUtente != '".addslashes($infoBlog["autore"])."' AND idBlog = '".addslashes($blogId)."'";
	$sqlco = mysqli_query($link,$sql3);
	while ($sqlcoResult = mysqli_fetch_assoc($sqlco)){
		$coautori[] = $sqlcoResult["nomeUtente"];
	}
} else {
	//se manca l'id del blog
	header("location:javascript://history.go(-1)");
	exit;
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
				if($('#divTemi').children().length>1){ //se c'è più di un argomento 
					$('#removeTemi').show();
				} else {
					$('#removeTemi').hide();
				}
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
							console.log(data)
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
		<div class="littleCard delete" id="deleteAccount">
			<div class="littleCardTitle">Sei sicuro?</div>
			<div class="littleCardBody">
				Il blog "<?= htmlentities($infoBlog["nomeBlog"])?>" verrà eliminato in maniera permanente. <br>
				<button class="button grey" onclick="closeDeleteBlog()">Annulla</button>
				<button class="button red" onclick="window.location='deleteBlog.php?id=<?=$blogId?>'">Elimina</button>
			</div>
		</div>
		<div class="all2" id="all2">
			<h2 style="font-size:40px;margin-bottom: 0px;">Modifica Blog</h2>
			<p class="sottotitolo">Qui sotto sono mostrati i dati del tuo blog<br>Seleziona una box per modificarla</p>
			<div class="formContainer">
				<form name="form" method="post" action="doModificaBlog.php" 
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
							if($_POST["errore"]=="inputLength"){
								echo "<div class='errorReg'>Attenzione! Uno o più dati inseriti superano i limiti di lunghezza<br> Per favore ricontrolla i tuoi dati</div>"; 
							}
							if($_POST["errore"]=="hack"){
								echo "<div class='errorReg'>Attenzione! Hai tentato di manomettere la form <br>Utilizza correttamente i dati che ti vengono forniti</div>"; 
							}
							if(($_POST["errore"]=="invalid")&&(isset($_POST["invalidType"]))) {
								echo "<div class='errorReg'>Attenzione! Argomento: ' ".htmlentities($_POST["invalidType"])." ' non rispetta il formato standard<br> Per favore ricontrolla i tuoi dati</div>";  
							};
							if(($_POST["errore"]=="coautore")&&(isset($_POST["valType"]))) {
								echo "<div class='errorReg'>Attenzione! Coautore: ' ".htmlentities($_POST["valType"])." ' non esiste o è il tuo nome utente<br> Per favore ricontrolla i tuoi dati</div>"; 
							};
						}
						?>
					<input type="hidden" name="idBlog" value="<?=$blogId?>"> 
					<label for=”titolo”>Titolo</label>
					<input type="text" name="titolo" maxlength="60" class="input lunghi" placeholder="Nome del Blog" value= "<?php echo htmlentities($infoBlog['nomeBlog']); ?>" required><br><br>
					<label for=”coautore”>Coautori</label>

					<?php
					$value=''; 
						for($i=0; $i<sizeof($coautori);$i++){ //show dei coautori
							if($i==sizeof($coautori)-1){
								$value .= $coautori[$i];  
							} else { //se coautori sono più di 1 aggiungo virgola e spazio
								$value .= $coautori[$i]. ', '; 
							}
						} 
					?>
					<div id="divCoautori">
						<input type="text" name="coautori" autocomplete="off" class="input lunghi" id="inputCoautori" placeholder="Inserisci i Coautori del Blog separati da una virgola ','" value="<?= htmlentities($value); ?>">
						<div class="autocompleteContainer autofill">
							<div></div>
						</div>
					</div>
					
					<h3 class="creaBlogTitles">Argomenti</h3>
					<label for=”categoria”>Argomenti del Blog</label>
					<div id="divTemi"><?php foreach ($args as $k){echo '<input type="text" style="margin-top:5px;" name="argomenti[]" class="input lunghi" id="here" maxlength="128" placeholder="Inserisci Argomento e Sottoargomento (opzionale) separati da una virgola ','" pattern="[a-zA-Z0-9]+(,[a-zA-Z0-9]+)*" value="'.htmlentities($k).'" required>';}?></div>
					
					<div class="containerButtons">
						<button type="button" class="button white sottotema" onclick="aggiungiTema();">+ Argomento</button>
						<button type="button" id="removeTemi" class="button remove sottotema" onclick="rimuoviTema();">- Rimuovi</button>
					</div>

					<label for=”descrizione”>Breve Descrizione del Blog</label>
					<textarea class="descrizioneBlog" name="descrizione" maxlength="120" placeholder="Scrivi qualcosa sul blog che stai creando" required><?php echo htmlentities($infoBlog['descrizione']); ?></textarea>
					<?php
					if($premium){
						echo '
					<input type="hidden" name="selection" value="premium">
					<h3 class="creaBlogTitles">Personalizzazione</h3>
					<div style="float:left;;width:50%;">
						<label for=”temaBlog”>Scegli il Tema</label>
						<select name="temaBlog" class="input corti select" style="width: 90%;" required>';
						foreach ($listaTemi as $i) { //temi
							if($i == $style["temaBlog"]) {
								$sel2 = "selected";
							} else {
								$sel2 = "";
							}
							echo '<option class="selectOption"'.$sel2.'>'.htmlentities($i).'</option>';
							
						}
						echo '</select>
					</div>
					<div style="float:right;;width:50%;">
						<label for=”font” style="margin-left: 10%">Scegli il Font</label>
						<select name="font" class="input corti select" style="width: 90%; margin-left: 10%;" required>';
						foreach ($listaFont as $j) { //fonts
							if($j=='Roboto (default)'){
								$font = "roboto";
							} else if ($j=='Open Sans'){
								$font = "openSans";
							} else if ($j=='Lato'){
								$font = "lato";
							} else if ($j=='Noto Sans JP'){
								$font = "noto";
							}
							if($j == $style["fontBlog"]) {
								$sel2 = "selected";
							} else {
								$sel2 = "";
							}
							echo '<option class="selectOption '.$font.'"'.$sel2.'>'.htmlentities($j).'</option>';
						}
						echo '</select>
					</div>
					<br style="clear:both;"/>';
					};
					?>
					<input type="submit" value="Modifica Blog" class="button white" style="float: right;margin-right: 0;margin-top: 30px;">
					<button type="button" class="button grey" style="float: right;margin-top: 30px;margin-bottom: 30px;" onclick="window.history.back();">Annulla</button>
					<button type="button" class="button blogDelete" style="margin-top: 30px;" onclick="deleteBlog()">Elimina Blog</button>
				</form>
			</div>
		</div>
	</body>
</html>