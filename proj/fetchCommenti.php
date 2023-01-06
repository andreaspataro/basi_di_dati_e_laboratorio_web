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

$output = '';
$commentId = '';
if(isset($_POST["idPost"]) && isset($_POST["lastCommentId"])){
	//controllo se id post esiste
	$sqlPost = getresult("SELECT * FROM post WHERE id = '".addslashes($_POST["idPost"])."'");
	if (!$sqlPost){
		//se post non esiste
		exit;	
	}
	if(isset($_SESSION["nomeUtente"])){
		//query di controllo se utente è proprietario del blog e quindi del post
		$owner = getresult("SELECT * FROM partecipanti WHERE nomeUtente = '".addslashes($_SESSION["nomeUtente"])."'");
	}
	//estrazione stile blog
	$idBlog = $sqlPost["idBlog"];
	$blogStyle = getresult("SELECT temaBlog FROM personalizzazione WHERE idBlog = '".addslashes($idBlog)."'")["temaBlog"];
	$sql = "SELECT * FROM commenti WHERE idPost = '".addslashes($_POST["idPost"])."' AND id < '".addslashes($_POST["lastCommentId"])."' ORDER BY dataCreazione DESC LIMIT 3";
	$result = mysqli_query($link, $sql);
	if(mysqli_num_rows($result) > 0){
		while($commenti = mysqli_fetch_assoc($result)){
			$commentoId = $commenti["id"];
			$date = htmlentities($commenti["dataCreazione"]);
			//conversione timestamp in data e ora
			$timestamp = strtotime($date);
			$data = date('d/m/Y', $timestamp);
			$ora = date('H:i', $timestamp);
			//output ajax
			$output .= '
				<div class="commentoUtente" id="'.htmlentities($commentoId).'">
				<h3 class="nomeUtenteCommento">'.htmlentities($commenti["autore"]).' <small class="dataOraPost">'.$data.' '.$ora.'</small>';
			if(isset($_SESSION["nomeUtente"])){
				if(($commenti["autore"] == $_SESSION["nomeUtente"]) || (isset($owner))){
					$output .= '<a class="redLink" id="delComm" data-id="'.htmlentities($commentoId).'">&nbsp;elimina</a>';
				}
			}
			$output .= '
				</h3>
				<p class="contenutoCommento">'.htmlentities($commenti["contenuto"]).'</p>
				</div>
			';
		}
		$output .= '
			<div id="removeRow">
			<button class="button mostra grey ';
			if(isset($blogStyle)){
				if($blogStyle == 'Tema Scuro'){
					$output .= 'neroButtons2';
				}
			}
		$output .='"name="buttonMore" id="buttonMore" data-comm="'.addslashes(htmlentities($commentoId)).'">Mostra commenti precedenti</button></div>
		';
		echo $output;
	}
}