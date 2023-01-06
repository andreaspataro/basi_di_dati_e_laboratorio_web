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

if (empty($_GET["id"])){
	//se manca l'id del blog da eliminare
	header("location:javascript://history.go(-1)");
	exit;
}

//dati
$idBlog = htmlentities($_GET["id"]); //id del blog da eliminare 
$nomeUtente = $_SESSION["nomeUtente"]; //nome utente che sta compiendo l'eliminazione

//query di controllo se utente è proprietario o coautore del blog
$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($nomeUtente)."'";
$controlloRes = getresult($controllo);
if(empty($controlloRes)){
	//se utente non è proprietario
	header("location:javascript://history.go(-1)"); 
	exit;
}

//query di estrazione nome del blog
$nomeBlog = getresult("SELECT nomeBlog FROM blog WHERE id = '".addslashes($idBlog)."'")["nomeBlog"];
//query di eliminazione del blog
$sql = "DELETE FROM blog WHERE id = '".addslashes($idBlog)."'";
if (mysqli_query($link, $sql)) {
	echo "Record deleted successfully";
} else {
	echo "Error deleting record: " . mysqli_error($link);
	exit;
}

//directory che contiene le foto dei post del blog
$dir = 'pics/'.$idBlog;
if(file_exists($dir)){ //se blog ha post con foto  
	//elimino foto riguardanti i post del blog che sto eliminando
	rmrf($dir);	
}

header("location:mieiBlog.php?success=delBlog&name=$nomeBlog");