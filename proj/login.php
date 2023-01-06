<?php
require_once "config.php";
require_once "connect.php";

//raccolta dati
$nome = $_POST["nome"]; 
$password = $_POST["password"];

//query di controllo se i dati di login corrispondono a quelli presenti nel db
$sql = "SELECT id FROM utente WHERE nomeUtente = '".addslashes($nome)."' AND password = '".addslashes(hash("sha256", $password))."'";
$result = mysqli_query($link,$sql);
$riga = mysqli_fetch_assoc($result);

if(!$riga){
	//se i dati non corrispondono
	header("location:accesso.php?errore=login");
	exit;
}
else{
	//login e redirect
	$_SESSION["idUtente"] = $riga["id"];
	$_SESSION["nomeUtente"] = $nome;
	header("location:mieiBlog.php");
}
