<?php
require_once "config.php";
require_once "connect.php";

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//dati in comune account gratis e premium
$id = $_SESSION['idUtente']; //id utente corrente
$newUsername = $_POST["username"]; //username modificato
$newEmail = $_POST["email"]; //email modificata 
$newTelefono = $_POST["telefono"]; //telefono modificato
$newDocumento = $_POST["documento"]; //documento modificato

//query di controllo se username è già in uso
$query = "SELECT * FROM utente WHERE nomeUtente = '".addslashes($newUsername)."' AND id != '".addslashes($id)."'"; 
$risultato = mysqli_query($link,$query);
$nomeTrovato = mysqli_fetch_assoc($risultato);

//funzione di validazione dati gratis 
function controlliGratis($username, $email, $telefono, $documento, $nomeTrovato) {
	if($nomeTrovato){ //controllo se username è già in uso
		header("location:modificaAccount.php?errore=username");
		return false;
	};
	//controlli lato server se viene "bucato" html
	//controllo lunghezza input
	if((strlen($username)>30)||(strlen($email)>128)||(strlen($telefono)>15)||(strlen($documento)>9)){
		header("location:modificaAccount.php?errore=inputLength");
		return false;	
	}
	//validazione username
	if(preg_match("/^[a-zA-Z0-9]*([._]?[a-zA-Z0-9]+)*$/", $username)===0){
		header("location:modificaAccount.php?errore=val&valType=Username%20inserito");
		return false;	
	}
	//validazione email
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header("location:modificaAccount.php?errore=val&valType=Email%20inserita");
		return false;			
	}
	//validazione telefono
	if(preg_match("/^[0-9]+$/", $telefono)===0) {
		header("location:modificaAccount.php?errore=val&valType=Telefono%20inserito");
		return false;			
	}
	//validazione documento
	if(preg_match("/^[a-zA-Z]{2}[0-9]{5}[a-zA-Z]{2}$/", $documento)===0){
		header("location:modificaAccount.php?errore=val&valType=Documento%20inserito");
		return false;	
	}
	//controlli superati con successo
	return true;
}

//funzione di validazione dati premium 
function controlliPremium($carta, $nominativo, $scadenza, $CVV) {
	//controlli lato server se viene "bucato" html
	//controllo lunghezza input
	if((strlen($carta)>16)||(strlen($nominativo)>128)||(strlen($CVV)>3)){
		header("location:modificaAccount.php?errore=inputLength");
		return false;
	}
	//validazione carta di credito
	if(preg_match("/^[0-9]{16}$/", $carta)===0){
		header("location:modificaAccount.php?errore=val&valType=Carta%20inserita");
		return false;	
	}
	//validazione CVV carta di credito
	if(preg_match("/^[0-9]{3}$/", $CVV)===0){
		header("location:modificaAccount.php?errore=val&valType=CVV%20inserito");
		return false;	
	}
	//validazione nominativo carta di credito
	if(preg_match("/^[a-zA-Z]*(\s?[a-zA-Z]+)*$/", $nominativo)===0){
		header("location:modificaAccount.php?errore=val&valType=Nominativo%20inserito");
		return false;	
	}
	//validazione data di scadenza carta di creditos
	if(preg_match("/^[0-9]{4}[-\/][01]?\d[-\/][0-3]?\d$/", $scadenza)===0){
		header("location:modificaAccount.php?errore=val&valType=Scadenza%20inserita");
		return false;	
	}
	//controlli superati con successo
	return true;	
}

//account gratis
if (!isset($_POST["selection"])) {
	if (!$newUsername || !$newEmail || !$newTelefono || !$newDocumento) { //controllo inserimento campi
		header("location:modificaAccount.php?errore=campi");
		exit;
	};
	//chiamo funzione di controllo input
	$validazioneGratis = controlliGratis($newUsername, $newEmail, $newTelefono, $newDocumento, $nomeTrovato);
	if(!$validazioneGratis) { //se non vengono superati i controlli
		exit;
	} else {
		//query di update dati se supera controlli
		$stmt = $link->prepare("UPDATE utente SET nomeUtente = ?, email = ?, telefono = ?, estremiDocumento = ? WHERE id = '$id'");
		$stmt->bind_param('ssis', $newUsername, $newEmail, $newTelefono, $newDocumento);
		$result = $stmt->execute();
		//aggiorno nome utente in sessione
		unset($_SESSION["nomeUtente"]);
		$_SESSION["nomeUtente"] = $newUsername;
		//controllo su esito query
		if($result) {
			header("location:modificaAccount.php?success=1");
			exit;
		} else {
			echo "Errore: " . mysqli_error($link);
		};
	};
};

//account premium
if(isset($_POST["selection"]) && $_POST["selection"]=="premium"){
	$newCarta = $_POST["carta"]; //carta modificata 
	$newNominativo = $_POST["nominativo"]; //nominativo modificato
	$newScadenza = $_POST["scadenza"]; //scadenza modificata
	$newCVV = $_POST["CVV"]; //cvv modificato
	//controllo inserimento campi
	if (!$newUsername || !$newEmail || !$newTelefono || !$newDocumento || !$newCarta || !$newNominativo || !$newScadenza || !$newCVV){
		header("location:modificaAccount.php?errore=campi");
		exit;	
	};
	//chiamo funzioni di controllo input 
	$validazionePremium1 = controlliGratis($newUsername, $newEmail, $newTelefono, $newDocumento, $nomeTrovato);
	$validazionePremium2 = controlliPremium($newCarta, $newNominativo, $newScadenza, $newCVV);
	if((!$validazionePremium1) || (!$validazionePremium2)) { //se non vengono superati i controlli
		exit;
	} else {
		//query di inserimento dati se supera controlli
		$stmt = $link->prepare("UPDATE utente SET nomeUtente = ?, email = ?, telefono = ?, estremiDocumento = ?, numCartaCredito = ?, nomeCartaCredito = ?, scadenzaCartaCredito = ?, CVVCartaCredito = ? WHERE id = '$id'");
		$stmt->bind_param('ssisissi', $newUsername, $newEmail, $newTelefono, $newDocumento, $newCarta, $newNominativo, $newScadenza, $newCVV);
		$result = $stmt->execute();
		//aggiorno nome utente in sessione
		unset($_SESSION["nomeUtente"]);
		$_SESSION["nomeUtente"] = $newUsername;
		//controllo su esito query
		if($result) {
			header("location:modificaAccount.php?success=1");
			exit;
		} else {
			echo "Errore: " . mysqli_error($link);
		}
	}
}