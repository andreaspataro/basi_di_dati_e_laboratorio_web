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

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//raccolta dati
$oldPass = $_POST["oldPassword"]; //vecchia password
$newPass = $_POST["newPassword"]; //nuova password
$confirm = $_POST["confirm"]; //conferma della password

$id = $_SESSION['idUtente']; //id utente corrente

//controlli sui campi
if(!$oldPass||!$newPass||!$confirm){
	//se campi sono vuoti
	header("location:modificaPassword.php?errore=inputLength");
	exit;
}

if((strlen($oldPass)>128)||(strlen($newPass)>128)||(strlen($confirm)>128)){
	//se campi superano il limite di lunghezza
	header("location:modificaPassword.php?errore=inputLength");
	exit;
}
//query di estrazione password dell'utente corrente prima della modifica
$sql = getresult("SELECT password FROM utente WHERE id = '".addslashes($id)."'")["password"];

if(hash("sha256", $oldPass)==$sql){ //se password vecchia immessa è uguale a quella estratta dal db
	if($newPass == $confirm){ //se la nuova password corrisponde alla conferma
		$password = hash("sha256", $newPass); //hash della nuova password
		//query di update della password in tabella utente
		$stmt = $link->prepare("UPDATE utente SET password = ? WHERE id = '".addslashes($id)."'");
		$stmt->bind_param('s', $password);
		$result = $stmt->execute();	
		if($result) {
			header("location:modificaAccount.php?success=1");
			exit;
		} else {
			echo "Errore: " . mysqli_error($link);
		}
	} else {
		//se la nuova password non corrisponde alla conferma
		header("location:modificaPassword.php?errore=confirm");
		exit;
	}
} else {
	//se la vecchia password immessa non è uguale a quella estratta dal db
	header("location:modificaPassword.php?errore=oldPass");
	exit;
}