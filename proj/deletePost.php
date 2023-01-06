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

function rmrf($dir) { 
	/*funzione creata con aggiunte dalle dispense e dal web*/
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

if (empty($_GET["idPost"])){
	//se manca l'id del post da eliminare
	header("location:javascript://history.go(-1)");
	exit;
}

//dati
$idPost = htmlentities($_GET["idPost"]); //id del post da eliminare
//id del blog che contiene il post da eliminare
$idBlog = getresult("SELECT idBlog FROM post WHERE id = '".addslashes($idPost)."'")["idBlog"]; 
$nomeUtente = $_SESSION["nomeUtente"]; //nome utente che compie l'eliminazione

//query di controllo se utente è proprietario o coautore del blog
$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($nomeUtente)."'";
$controlloRes = getresult($controllo);
if(empty($controlloRes)){
	//se utente non è proprietario
	header("location:javascript://history.go(-1)"); 
	exit;
}

//query di eliminazione post 
$sql = "DELETE FROM post WHERE id = '".addslashes($idPost)."'";
if (mysqli_query($link, $sql)) {
	echo "Record deleted successfully";
} else {
	echo "Error deleting record: " . mysqli_error($link);
	exit;
}

//directory che contiene le foto del post
$dir = 'pics/'.$idBlog.'/'.$idPost;
if(file_exists($dir)){ //se post ha foto  
	//elimino foto 
	rmrf($dir); 
}
header("location:blog.php?idBlog=$idBlog");