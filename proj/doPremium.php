<?php
require_once "config.php";
require_once "connect.php";

//funzione di validazione dati premium 
function controlliPremium($carta, $nominativo, $scadenza, $CVV) {
	//controlli lato server se viene "bucato" html
	//controllo lunghezza input
	if((strlen($carta)>16)||(strlen($nominativo)>128)||(strlen($CVV)>3)){
		header("location:premiumAccount.php?errore=inputLength");
		return false;
	}
	//validazione carta di credito
	if(preg_match("/^[0-9]{16}$/", $carta)===0){
		header("location:premiumAccount.php?errore=val&valType=Carta%20inserita");
		return false;	
	}
	//validazione CVV carta di credito
	if(preg_match("/^[0-9]{3}$/", $CVV)===0){
		header("location:premiumAccount.php?errore=val&valType=CVV%20inserito");
		return false;	
	}
	//validazione nominativo carta di credito
	if(preg_match("/^[a-zA-Z]*(\s?[a-zA-Z]+)*$/", $nominativo)===0){
		header("location:premiumAccount.php?errore=val&valType=Nominativo%20inserito");
		return false;	
	}
	//validazione data di scadenza carta di creditos
	if(preg_match("/^[0-9]{4}[-\/][01]?\d[-\/][0-3]?\d$/", $scadenza)===0){
		header("location:premiumAccount.php?errore=val&valType=Scadenza%20inserita");
		return false;	
	}
	//controlli superati con successo
	return true;	
}

if (!isset($_SESSION["idUtente"])){
  //se utente non è loggato
  header("location:accesso.php");
  exit;
}

//dati account premium
$id = $_SESSION['idUtente']; //id utente corrente
$carta = $_POST["carta"]; //numero carta di credito
$nominativo = $_POST["nominativo"]; //nominativo carta di credito
$scadenza = $_POST["scadenza"]; //scadenza carta di credito
$CVV = $_POST["CVV"]; //cvv carta di credito

//controllo se sono stati inseriti tutti i campi
if (!$carta || !$nominativo || !$scadenza || !$CVV){
	header("location:premiumAccount.php?errore=campi");
	exit;	
};
//chiamo funzione di controllo input 
$validazionePremium = controlliPremium($carta, $nominativo, $scadenza, $CVV);
if(!$validazionePremium) { //se non vengono superati i controlli
	exit;
} else {
	//query di update dei dati premium in tabella utente
	$stmt = $link->prepare("UPDATE utente SET numCartaCredito = ?, nomeCartaCredito = ?, scadenzaCartaCredito = ?, CVVCartaCredito = ? WHERE id = '$id'");
	$stmt->bind_param('issi', $carta, $nominativo, $scadenza, $CVV);
	$result = $stmt->execute();
	//controllo su esito query
	if($result) {
		header("location:modificaAccount.php?success=premium");
	} else {
		echo "Errore: " . mysqli_error($link);
	}
}