<?php
require_once "config.php";
require_once "connect.php";

if (isset($_SESSION["idUtente"])){
	//se utente è logato faccio logout
	unset($_SESSION["idUtente"]);
	unset($_SESSION["nomeUtente"]);
}

//funzione di validazione dati gratis 
function controlliGratis($selection, $username, $passwordRaw, $email, $telefono, $documento, $nomeTrovato) {
	if($nomeTrovato){ //controllo se username è già in uso
		header("location:registrazione.php?errore=username&selezione=$selection");
		return false;
	};
	//controlli lato server se viene "bucato" html
	//controllo lunghezza input
	if((strlen($username)>30)||(strlen($email)>128)||(strlen($passwordRaw)>128)||(strlen($telefono)>15)||(strlen($documento)>9)){
		header("location:registrazione.php?errore=inputLength&selezione=$selection");
		return false;	
	}
	//validazione username
	if(preg_match("/^[a-zA-Z0-9]*([._]?[a-zA-Z0-9]+)*$/", $username)===0){
		header("location:registrazione.php?errore=val&valType=Username%20inserito&selezione=$selection");
		return false;	
	}
	//validazione email
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header("location:registrazione.php?errore=val&valType=Email%20inserita&selezione=$selection");
		return false;			
	}
	//validazione telefono
	if(preg_match("/^[0-9]+$/", $telefono)===0) {
		header("location:registrazione.php?errore=val&valType=Telefono%20inserito&selezione=$selection");
		return false;			
	}
	//validazione documento
	if(preg_match("/^[a-zA-Z]{2}[0-9]{5}[a-zA-Z]{2}$/", $documento)===0){
		header("location:registrazione.php?errore=val&valType=Documento%20inserito&selezione=$selection");
		return false;	
	}
	//controlli superati con successo
	return true;
}

//funzione di validazione dati premium 
function controlliPremium($selection, $carta, $nominativo, $scadenza, $CVV) {
	//controlli lato server se viene "bucato" html
	//controllo lunghezza input
	if((strlen($carta)>16)||(strlen($nominativo)>128)||(strlen($CVV)>3)){
		header("location:registrazione.php?errore=inputLength&selezione=$selection");
		return false;
	}
	//validazione carta di credito
	if(preg_match("/^[0-9]{16}$/", $carta)===0){
		header("location:registrazione.php?errore=val&valType=Carta%20inserita&selezione=$selection");
		return false;	
	}
	//validazione CVV carta di credito
	if(preg_match("/^[0-9]{3}$/", $CVV)===0){
		header("location:registrazione.php?errore=val&valType=CVV%20inserito&selezione=$selection");
		return false;	
	}
	//validazione nominativo carta di credito
	if(preg_match("/^[a-zA-Z]*(\s?[a-zA-Z]+)*$/", $nominativo)===0){
		header("location:registrazione.php?errore=val&valType=Nominativo%20inserito&selezione=$selection");
		return false;	
	}
	//validazione data di scadenza carta di credito
	if(preg_match("/^[0-9]{4}[-\/][01]?\d[-\/][0-3]?\d$/", $scadenza)===0){
		header("location:registrazione.php?errore=val&valType=Scadenza%20inserita&selezione=$selection");
		return false;	
	}
	//controlli superati con successo
	return true;	
}

//dati in comune account gratis e premium
$username = $_POST["username"];
$passwordRaw = $_POST["password"];
$email = $_POST["email"];
$telefono = $_POST["telefono"];
$documento = $_POST["documento"];


//query di controllo se username è già in uso
$query = "SELECT * FROM utente WHERE nomeUtente = '".addslashes($username)."'";
$risultato = mysqli_query($link,$query);
$nomeTrovato = mysqli_fetch_assoc($risultato);

//account gratis
if (!isset($_POST["selection"])) { 
	if(!$username || !$passwordRaw || !$email || !$telefono || !$documento) { //controllo inserimento campi
		header("location:registrazione.php?errore=campi&selezione=gratis");
		exit;
	};
	$validazioneGratis = controlliGratis("gratis", $username, $passwordRaw, $email, $telefono, $documento, $nomeTrovato);
	if(!$validazioneGratis) { //se non vengono superati i controlli
		exit;
	} else { //controlli superati con successo
		//query di inserimento dati
		//hash password
		$password = hash("sha256", $passwordRaw);
		$stmt = $link->prepare("INSERT INTO utente (nomeUtente, password, email, telefono, estremiDocumento)
			VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('sssis', $username, $password, $email, $telefono, $documento);
		$result = $stmt->execute();
		//controllo su esito query
		if(!$result) { //errore query
			echo "Errore: " . mysqli_error();
		} else { //successo query
			//query di accesso con dati appena registrati
			$query = "SELECT id FROM utente WHERE nomeUtente = '".addslashes($username)."' AND password = '".addslashes($password)."'";
			//fai bindparam
			$queryResult = mysqli_query($link,$query);
			$riga = mysqli_fetch_assoc($queryResult);
			//salvo in sessione l'id e il nome dell'utente appena registrato
			$_SESSION["idUtente"] = $riga["id"];
			$_SESSION["nomeUtente"] = $username;
			header("location:mieiBlog.php?success=registrazione");
		}
	}
}	

//account premium
if(isset($_POST["selection"]) && $_POST["selection"]=="premium"){
	$carta = $_POST["carta"];
	$nominativo = $_POST["nominativo"];
	$scadenza = $_POST["scadenza"];
	$CVV = $_POST["CVV"];
	//controllo inserimento campi
	if (!$username || !$passwordRaw || !$email || !$telefono || !$documento || !$carta || !$nominativo || !$scadenza||!$CVV){
		header("location:registrazione.php?errore=campi&selezione=premium");
		exit;	
	};
	$validazionePremium1 = controlliGratis("premium", $username, $passwordRaw, $email, $telefono, $documento, $nomeTrovato);
	$validazionePremium2 = controlliPremium("premium", $carta, $nominativo, $scadenza, $CVV);
	if((!$validazionePremium1) || (!$validazionePremium2)) { //se non vengono superati i controlli
		exit;
	} else { //controlli superati con successo
		//hash di password
		$password = hash("sha256", $passwordRaw);
		//query di inserimento dati
		$stmt = $link->prepare("INSERT INTO utente (nomeUtente, password, email, telefono, estremiDocumento, numCartaCredito, nomeCartaCredito, scadenzaCartaCredito, CVVCartaCredito)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('sssisissi', $username, $password, $email, $telefono, $documento, $carta, $nominativo, $scadenza, 
			$CVV);
		$result = $stmt->execute();
		//controllo su esito query
		if(!$result) { //errore query
			echo "Errore: " . mysqli_error($link);
		} else { //successo query
			//query di accesso con dati appena registrati
			$query = "SELECT id FROM utente WHERE nomeUtente = '".addslashes($username)."' AND password = '".addslashes($password)."'";
			//fai bindparam
			$queryResult = mysqli_query($link,$query);
			$riga = mysqli_fetch_assoc($queryResult);
			//salvo in sessione l'id e il nome dell'utente appena registrato
			$_SESSION["idUtente"] = $riga["id"];
			$_SESSION["nomeUtente"] = $username;
			header("location:mieiBlog.php?success=registrazione");
		}
	}
}