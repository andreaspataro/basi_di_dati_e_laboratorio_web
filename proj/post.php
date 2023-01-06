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


if(isset($_GET["idBlog"]) && isset($_GET["idPost"])){  //se ci sono id del post e id del blog
	//ricavo informazioni sul post
	$idBlog = htmlentities($_GET["idBlog"]); //id blog
	$idPost = htmlentities($_GET["idPost"]); //id post
	if(isset($_SESSION["nomeUtente"])){
		//query di controllo se utente è proprietario del blog e quindi del post
		$owner = getresult("SELECT * FROM partecipanti WHERE nomeUtente = '".addslashes($_SESSION["nomeUtente"])."'");
	}
	//query di controllo se esiste post con l'id post e l'id blog ricevuti
	//match anche su idBlog perché mi serve per controllo in caso venga modificato url
	$postData = getresult("SELECT * FROM post WHERE id = '".addslashes($idPost)."' AND idBlog = '".addslashes($idBlog)."'");
	//query di estrazione directory immagini del post da tabella immagini
	$postPics = "SELECT directory FROM immagini WHERE idPost = '".addslashes($idPost)."'";
	$picsRes = mysqli_query($link, $postPics);
	$pics = [];
	while($picsRow = mysqli_fetch_assoc($picsRes)){
		$pics[] = $picsRow["directory"];
	}
	//query di estrazione tema e font da tabella personalizzazione
	$style = "SELECT * FROM personalizzazione WHERE idBlog = '".addslashes($idBlog)."'";
	$styleRes = mysqli_query($link, $style);
	$styleRow = mysqli_fetch_assoc($styleRes);
	if(isset($styleRow['temaBlog'])){ //se esiste tema 
		$blogStyle = $styleRow['temaBlog']; 
	}
	if(isset($styleRow['fontBlog'])){ //se esiste font
		$blogFont = $styleRow['fontBlog']; 
	}
	//conto quanti commenti ci sono
	$commentiCount = getresult("SELECT count(id) FROM commenti WHERE idPost = '".addslashes($idPost)."'")["count(id)"];
	//query di estrazione commenti del post da tabella commenti dal più recente
	$commentiSql = "SELECT * FROM commenti WHERE idPost = '".addslashes($idPost)."' ORDER BY dataCreazione DESC LIMIT 3"; 
	$commentiRes = mysqli_query($link, $commentiSql);
	$commenti = [];
	while($commentiRow = mysqli_fetch_assoc($commentiRes)){
		$commenti[] = $commentiRow;
	}
}

if(!isset($postData)){
	//se post non esiste
	header("location:javascript://history.go(-1)");
} 
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="home.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="animations.js"></script>
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
	<body class="bodyBlog <?php if(isset($blogStyle)){if($blogStyle == 'Tema Scuro'){echo 'nero';}if($blogStyle == 'Tema Verde'){echo 'verde';}}if(isset($blogFont)){if($blogFont == "Open Sans"){echo " openSans";}if($blogFont == "Lato"){echo " lato";}if($blogFont == "Noto Sans JP"){echo " noto";}else {echo " roboto";}}?>">
		<?php
		$date = htmlentities($postData["dataCreazione"]);
		//conversione timestamp in data e ora
		$timestamp = strtotime($date);
		$data = date('d/m/Y', $timestamp);
		$ora = date('H:i', $timestamp);
		?>
		<div class="writtenPostContainer">
			<h1 class="blogTitle"><?= htmlentities($postData["titolo"]);?></h1>
			<h3 class="postSubtitle">scritto da <?= htmlentities($postData["autore"]); ?>, creato il <?=$data?> alle <?=$ora?></h3>
			<hr class="postDivider <?php if(isset($blogStyle)){if($blogStyle == "Tema Scuro"){echo "dividerNero";}}?>">
			<p class="contenutoPost">
				<?=str_replace("\n",'<br>',htmlentities($postData["contenuto"]))?>
			</p>
			<hr class="postDivider <?php if(isset($blogStyle)){if($blogStyle == "Tema Scuro"){echo "dividerNero";}}?>">
			<?php
			if(!empty($pics)){ //se post ha immagini
				echo '<div class="extPicsContainer">';
				foreach ($pics as $key => $value) { //scorro immagini
					echo '<div class="postedPicsContainer">';
					echo '<img src = "'.htmlentities($value).'" alt="Immagine di preview post" class="postedImages" onclick="window.location.href=\''.htmlentities($value).'\'">';
					echo '</div>'; 
				}
				echo '</div>';
				echo '<hr class="postDivider ';
				if(isset($blogStyle)){ 
					if($blogStyle == "Tema Scuro"){
						echo "dividerNero";
					}
				}
				echo '">';
			}
			if(isset($_SESSION["nomeUtente"])){ //se utente è loggato può commentare
				echo '<div class="littleCard commenti ';
			  	if(isset($blogStyle)){
					if($blogStyle == 'Tema Scuro'){
				  		echo 'neroCards';
					}
					if($blogStyle == 'Tema Verde'){
			  			echo 'verdeCards';
					}
				}
				echo '">';
				echo '<div class="littleCardTitle ';
				if(isset($blogStyle)){
					if($blogStyle == 'Tema Scuro'){
						echo 'neroCardsTitle';
					}
					if($blogStyle == 'Tema Verde'){
					  	echo 'verdeCardsTitle';
					}
				}
				echo '">';
				//commenti
				echo 'Lascia un commento</div>';
				echo '<div class="littleCardBody">';
				echo '<form name="commentoForm" id="commentoForm" method="post" action="">';
				echo '<textarea name="commento" class="inserisciCommento ';
				if(isset($blogStyle)){
					if($blogStyle == 'Tema Scuro'){echo 'neroComments';}
				}
				if(isset($blogFont)){
					if($blogFont == "Open Sans"){
						echo " openSans";
					}
					if($blogFont == "Lato"){
						echo " lato";
					}
					if($blogFont == "Noto Sans JP"){
						echo " noto";
					} else {
						echo " roboto";
					}
				}
				echo '" placeholder="Scrivi qualcosa..." maxlength="5000" id="contenuto" required></textarea>';
				echo '<input type="button" class="button blue commenti ajax" value="Invia">';
				echo '</form>';
				echo '</div>';
				echo '</div>';
			}
			if(!empty($commenti)){ //se esistono commenti per il post
				echo '<div id="divvone">';
				for($i=0;$i<sizeof($commenti);$i++){ 
					$idCommento = $commenti[$i]["id"]; //id commento
					$date = htmlentities($commenti[$i]["dataCreazione"]);
					//conversione timestamp in data e ora
					$timestamp = strtotime($date);
					$data = date('d/m/Y', $timestamp);
					$ora = date('H:i', $timestamp);
					echo '<div class="commentoUtente" id="'.htmlentities($idCommento).'">';
					echo '<h3 class="nomeUtenteCommento">'.htmlentities($commenti[$i]["autore"]).' <small class="dataOraPost">'.$data.' '.$ora.'</small> ';
					if(isset($_SESSION["nomeUtente"])){
						if(($commenti[$i]["autore"] == $_SESSION["nomeUtente"]) || (isset($owner))){
							echo '<a class="redLink" id="delComm" data-id="'.addslashes($idCommento).'">&nbsp;elimina</a>';
						}
					}
					echo'</h3>';
					echo '<p class="contenutoCommento">'.htmlentities($commenti[$i]["contenuto"]).'</p>';
					echo '</div>';
			 	}
			  	echo '</div>';
			  	if($commentiCount>3){
			  		echo '<div id="removeRow"><button class="button grey mostra ';
					if(isset($blogStyle)){
						if($blogStyle == 'Tema Scuro'){
							echo 'neroButtons2';
						}
					}
					echo '" name="buttonMore" id="buttonMore" data-comm="'.addslashes($idCommento).'">Mostra commenti precedenti</button></div><br><br>';
			  	}
			} else {
				echo '<div id="divvone">';
				echo '<div class="commentoUtente">';
				echo '<p class="contenutoCommento" id="emptyCommenti">Non ci sono ancora commenti</p>';
				echo '</div>';
				echo '</div>';
			}
			?>
		</div>
		<script type="text/javascript">
			$(".ajax").click(function(){//chiamata ajax a commentoPost.php all'invio del commento
				$.ajax({
					type: "POST",
					url: "commentoPost.php",
					data: {'idPost':<?=addslashes($idPost)?>,'contenuto':$("#contenuto").val()},
					dataType: "json",
				
					success: function(data){
						console.log(data);
						if($("#emptyCommenti")){ //se non ci sono ancora commenti 
							$("#emptyCommenti").hide();
							//prepend del commento ricevuto dalla chiamata ajax
							$("#divvone").prepend('<div class="commentoUtente" id="'+data.id+'"><h3 class="nomeUtenteCommento">'+data.autore+' <small class="dataOraPost">'+data.data+'</small> <a class="redLink" id="delComm" data-id="'+data.id+'">&nbsp;elimina</a></h3><p class="contenutoCommento">'+data.contenuto+'</p></div>'); 
							$("#contenuto").val(""); //reset dell'input
					  	} else { //se ci sono già commenti
							//prepend del commento ricevuto dalla chiamata ajax
							$("#divvone").prepend('<div class="commentoUtente" id="'+data.id+'"><h3 class="nomeUtenteCommento">'+data.autore+' <small class="dataOraPost">'+data.data+'</small> <a class="redLink" id="delComm" data-id="'+data.id+'">&nbspdata-id="'+data.id+'">&nbsp;elimina</a></h3><p class="contenutoCommento">'+data.contenuto+'</p></div>');
							$("#contenuto").val(""); //reset dell'input
					  	}
					}
				})
		  	});
		</script>
	</body>
	<footer style="margin-top: 50px;">
		<p>I contenuti presenti nel post "<?= htmlentities($postData["titolo"]); ?>" sono di proprietà di "<?= htmlentities($postData["titolo"]); ?>" o dell'autore "<?= htmlentities($postData["autore"]); ?>"</p>
	</footer>
</html>
<script>
$(document).ready(function(){
	$(document).on('click', '#buttonMore', function(){ //click 'mostra commenti precedenti'
		var lastCommId = $(this).data("comm"); //salvo l'id dell'ultimo commento caricato
		$('#buttonMore').html("Carico...");
		$.ajax({ //chiamata ajax
			url:"fetchCommenti.php",
			method:"POST",
			data:{lastCommentId:lastCommId, idPost:<?=addslashes($idPost)?>},
			dataType:"text",

			success: function(data){
				console.log(data);
				if(data != ''){ //se il fetch ha ritornato qualcosa
					$("#removeRow").remove();
					$("#divvone").append(data);
				} else { 
					//se il fetch non ha ritornato niente vuol dire che ho caricato tutti i commenti
					$("#buttonMore").html("Sono stati caricati tutti i commenti");
				}
			}
		});
	});
	$(document).on('click', '#delComm', function(){ //elimina commento
		var idCommento = $(this).data("id"); //salvo l'id del commento
		$.ajax({ //chiamata ajax
			url:"deleteCommento.php",
			method:"POST",
			data:{idCommento:idCommento},
			dataType:"text",

			success: function(data){
				console.log(data);
				if(data){ //se è stato eliminato il commento
					$("#"+idCommento).remove();
				}
			}
		});
	});
});
</script>