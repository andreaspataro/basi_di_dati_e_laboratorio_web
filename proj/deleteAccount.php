<?php
require_once "config.php";
require_once "connect.php";

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

//dati
$id = $_SESSION['idUtente']; //nome utente da eliminare
$nomeUtente = $_SESSION['nomeUtente']; //id utente da eliminare

//tiro fuori tutti gli id dei blog che ha questo utente
$query = "SELECT * FROM blog WHERE autore = '".addslashes($nomeUtente)."'";
$risultato = mysqli_query($link,$query);
while($blogs = mysqli_fetch_assoc($risultato)){
	$idBlog = $blogs["id"];
	//directory che contiene le foto dei post del blog
	$dir = 'pics/'.$blogs["id"];
	if(file_exists($dir)){ //se blog ha post con foto  
		//elimino foto riguardanti i post del blog che sto eliminando
		rmrf($dir);	
	}
}

//elimino utente dal db
$sql = "DELETE FROM utente WHERE id = '".addslashes($id)."'";
if (mysqli_query($link, $sql)) {
	echo "Record deleted successfully";
} else {
	echo "Error deleting record: " . mysqli_error($link);
}

//logout e redirect ad accesso.php
unset($_SESSION["idUtente"]);
unset($_SESSION["nomeUtente"]);
header("location:accesso.php?success=accountDeleted");