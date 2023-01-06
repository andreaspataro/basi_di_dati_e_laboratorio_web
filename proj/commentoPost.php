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

//dati
$commento = $_POST["contenuto"];
$idPost = $_POST["idPost"];
$nomeUtente = $_SESSION["nomeUtente"]; //nome utente

if(!$idPost || !$nomeUtente ||!$commento) { //controllo inserimento campi
	header("location:javascript://history.go(-1)");
	exit;
}

//controllo se idPost esiste
$sql = getresult("SELECT id, idBlog FROM post WHERE id = '".addslashes($idPost)."'");
if (!$sql){
	//se post non esiste torno a pagina precedente
	header("location:javascript://history.go(-1)");
	exit;	
} else {
	if(strlen($commento)>5000){
		//se commento è troppo lungo non permetto di inviarlo
		header("location:javascript://history.go(-1)");
		exit;
	}
	//query di inserimento commento in tabella commenti
	$stmt = $link->prepare("INSERT INTO commenti (contenuto, autore, idPost)
		VALUES (?, ?, ?)");
	$stmt->bind_param('ssi', $commento, $nomeUtente, $idPost);
	$result = $stmt->execute();
	if(!$result) { //errore query
		echo "Errore: " . mysqli_error($link);
		exit;
	} else {
		//estrazione commento appena inserito perché mi serve il timestamp
		$idCommento = mysqli_insert_id($link);
		$sql = getresult("SELECT * FROM commenti WHERE id = '".addslashes($idCommento)."'");
		$date = $sql["dataCreazione"];
		//conversione timestamp
		$timestamp = strtotime($date);
		$data = date('d/m/Y H:i', $timestamp);
		//creazione oggetto di ritorno
		$obj = ["id"=>$idCommento, "autore"=>$sql["autore"], "contenuto"=>$sql["contenuto"], "data"=>$data];
		//json encode
		echo json_encode($obj); 
	}
}