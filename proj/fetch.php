<?php
require_once "config.php";
require_once "connect.php";

$output = [];

if(!empty($_POST["keyword"])) { //se non è vuota la chiamata ajax
	//estrazione utenti simili a quello che è stato immesso nell'input
	$query ="SELECT nomeUtente FROM utente WHERE nomeUtente like '" .
	addslashes(htmlentities($_POST["keyword"])). "%' ORDER BY nomeUtente LIMIT 0,6";
	$result = mysqli_query($link,$query);
	if($result) {
		while($row = mysqli_fetch_row($result)){
			$output[] = $row[0]; 
		}
	}
}
//json encode del risultato
echo json_encode($output);