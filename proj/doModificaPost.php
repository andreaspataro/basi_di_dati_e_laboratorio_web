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

function controlli ($titolo, $contenuto, $idBlog, $nomeUtente, $idPost){
	if(empty($idPost)){
		//se input hidden che corrisponde all'id del post viene eliminato
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//query di controllo se utente è proprietario o coautore del blog
	$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($nomeUtente)."'";
	$controlloRes = getresult($controllo);
	if(empty($controlloRes)){
		//se utente non è proprietario o coautore
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//controllo dati se sono vuoti
	if(!$titolo || !$contenuto) { //controllo inserimento campi
		header("location:gestoreErrori.php?idPost=$idPost&errore=campi&dest=modificaPost");
		return false;
	}
	//controllo su lunghezza variabili
	if(strlen($titolo)>128){ //se titolo troppo lungo
		header("location:gestoreErrori.php?idPost=$idPost&errore=inputLengthTitle&dest=modificaPost");
		return false;	
	}
	if(strlen($contenuto)>20000){ //se contenuto troppo lungo
		header("location:gestoreErrori.php?idPost=$idPost&errore=inputLengthText&dest=modificaPost");
		return false;		
	}
	return true;
}


function rmrf($dir) { 
	/*funzione creata con aggiunte dal web*/
	//funzione che scorre la directory ed elimina le sottodirectory e i file al suo interno
	foreach (glob($dir) as $file) { //scorro elementi dentro alla directory
		if (is_dir($file)) { //se elemento è una directory
	  		rmrf("$file/*"); //richiamo funzione entrando dentro alla directory
	  		rmdir($file); //elimino directory
		} else {
	  		unlink($file); //se elemento è un file lo elimino
		}
	}
}

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//raccolta dati
$titolo = $_POST['titolo']; //titolo del post 
$contenuto = $_POST['post']; //contenuto del post
$idPost = $_POST['idPost']; //id del post
//estrazione id del blog che contiene il post
$idBlog = getresult("SELECT idBlog FROM post WHERE id = '".addslashes($idPost)."'")["idBlog"];
$nomeUtente = $_SESSION["nomeUtente"]; //nome utente che sta modificando il post

//chiamata funzione di controlli
$validazione = controlli($titolo, $contenuto, $idBlog, $nomeUtente, $idPost);

if($validazione){
	//se hanno successo i controlli
	if($_FILES["pic"]["error"][0] == 0){ //se viene caricata almeno una foto
		//controlli sulle foto caricate
		$countfiles = count($_FILES['pic']['name']); //numero delle foto 
		$formati = ['jpg', 'jpeg', 'jfif', 'png']; //array dei formati consentiti
		if($countfiles > 2){ //se le foto sono più di due 
			header("location:gestoreErrori.php?errore=fileNum&dest=modificaPost&idPost=$idPost");
			exit;
		}
		for($j=0;$j<$countfiles;$j++){ //scorro le foto
			//estrazione formato per ogni foto
			$dato = preg_split('/\.(?=[^\.]+$)/', $_FILES['pic']['name'][$j])[1];
			if(!in_array($dato, $formati)){ //se formato non è presente nell'array
				header("location:gestoreErrori.php?errore=formato&dest=modificaPost&idPost=$idPost");
				exit;
			}
			//controllo grandezza immagini 500kb max
			if($_FILES['pic']['size'][$j]>500000){
				header("location:gestoreErrori.php?errore=size&dest=modificaPost&idPost=$idPost");
				exit;
			}
		}
	}
	//query di inserimento dati nella tabella post
	$stmt = $link->prepare("UPDATE post SET idBlog = ?, titolo = ?, contenuto = ?, autore = ? WHERE id = '$idPost'");
	$stmt->bind_param('isss', $idBlog, $titolo, $contenuto, $nomeUtente);
	$result = $stmt->execute();
	if(!$result) { //errore query
		echo "Errore: " . mysqli_error($link);
		exit;
	} else {
		if($_FILES["pic"]["error"][0] == 0){ //se viene caricata almeno una foto
			//salvo immagini 
			$dir = 'pics/'.$idBlog.'/'.$idPost.'/'; //directory
			if(file_exists($dir)){ //se directory esiste rimuovo la directory e le foto al suo interno
				rmrf($dir);
				//prima di inserire le directory delle immagini nel db elimino quelle precedenti 
				$delPics = "DELETE FROM immagini WHERE idPost = '".addslashes($idPost)."'";
				$delRes = mysqli_query($link, $delPics);
				if(!$delRes){
					echo "Error deleting record: " . mysqli_error($link);
					exit;
				}
			}
			for($i=0;$i<$countfiles;$i++){ //scorro le foto
				//prendo il nome delle foto
				$info = pathinfo($_FILES['pic']['name'][$i]);
				$newname = $info['basename'];
				mkdir('pics/'.$idBlog.'/'.$idPost, 0777, true); //creo directory
				$target = 'pics/'.$idBlog.'/'.$idPost.'/'.$newname; //path
				move_uploaded_file($_FILES['pic']['tmp_name'][$i], $target); //metto i file nella directory
				//inserisco in tabella immagini la directory e idPost per ogni immagine
				$stmt2 = $link->prepare("INSERT INTO immagini (directory, idPost)
					VALUES (?, ?)");
				$stmt2->bind_param('si', $target, $idPost);
				$result2 = $stmt2->execute();
				if(!$result2) { //errore query
					echo "Errore: " . mysqli_error($link);
					exit;
				}
			}
		}	
		header("location:blog.php?idBlog=$idBlog");
	}
}