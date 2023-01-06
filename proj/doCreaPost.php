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

function controlli ($titolo, $contenuto, $idBlog, $nomeUtente){
	//se input hidden viene eliminato
	if(empty($idBlog)){
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//controllo se sei proprietario o coautore del blog
	$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($nomeUtente)."'";
	$controlloRes = getresult($controllo);
	if(empty($controlloRes)){
		//non visualizzo l'errore con un alert perché si tratta di una manomissione
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//controllo dati se sono vuoti
	if(!$titolo || !$contenuto) { //controllo inserimento campi
		header("location:gestoreErrori.php?idBlog=$idBlog&errore=campi&dest=creaPost");
		return false;
	}
	//controllo su lunghezza variabili
	if(strlen($titolo)>128){
		header("location:gestoreErrori.php?idBlog=$idBlog&errore=inputLengthTitle&dest=creaPost");
		return false;	
	}
	if(strlen($contenuto)>20000) {
		header("location:gestoreErrori.php?idBlog=$idBlog&errore=inputLengthText&dest=creaPost");
		return false;		
	}
	return true;
}

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//raccolta dati 
$titolo = $_POST['titolo']; //titolo del post
$contenuto = $_POST['post']; //contenuto del post
$idBlog = $_POST['idBlog']; //id del blog che contiene il post
$nomeUtente = $_SESSION["nomeUtente"]; //nome utente che sta creando il post

//chiamata a funzione di controllo
$validazione = controlli($titolo, $contenuto, $idBlog, $nomeUtente);
//controllo successo validazione
if($validazione){
	//se passa i controlli
	if($_FILES["pic"]["error"][0] == 0){ //se viene caricata almeno una foto
		//controlli sulle foto caricate
		$countfiles = count($_FILES['pic']['name']); //numero delle foto 
		$formati = ['jpg', 'jpeg', 'jfif', 'png']; //array dei formati consentiti
		if($countfiles > 2){ //se le foto sono più di due 
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=fileNum&dest=creaPost");
			exit;
		}
		for($j=0;$j<$countfiles;$j++){ //scorro le foto
			//estrazione formato per ogni foto
			$dato = preg_split('/\.(?=[^\.]+$)/', $_FILES['pic']['name'][$j])[1]; 
			if(!in_array($dato, $formati)){ //se formato non è presente nell'array
				header("location:gestoreErrori.php?idBlog=$idBlog&errore=formato&dest=creaPost");
				exit;
			}
			//controllo grandezza immagini 500kb max
			if($_FILES['pic']['size'][$j]>500000){
				header("location:gestoreErrori.php?idBlog=$idBlog&errore=size&dest=creaPost");
				exit;	
			}
		}

	}
	//query di inserimento dati nella tabella post
	$stmt = $link->prepare("INSERT INTO post (idBlog, titolo, contenuto, autore)
		VALUES (?, ?, ?, ?)");
	$stmt->bind_param('isss', $idBlog, $titolo, $contenuto, $nomeUtente);
	$result = $stmt->execute();
	if(!$result) { //errore query
		echo "Errore: " . mysqli_error($link);
		exit;
	} else {
		if($_FILES["pic"]["error"][0] == 0){ //se viene caricata almeno una foto
			//salvo immagini
			$idPost = mysqli_insert_id($link); //id del post appena inserito
			for($i=0;$i<$countfiles;$i++){ //scorro le foto
				//prendo il nome delle foto
				$info = pathinfo($_FILES['pic']['name'][$i]); 
				$newname = $info['basename'];
				if (!file_exists('pics/'.$idBlog.'/'.$idPost)) {
					//se directory non esiste la creo
					mkdir('pics/'.$idBlog.'/'.$idPost, 0777, true);
				}
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