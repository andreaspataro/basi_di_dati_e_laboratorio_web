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

if(isset($_POST["idCommento"])){
	//controllo se id commento esiste
	$sqlCommento = getresult("SELECT * FROM commenti WHERE id = '".addslashes($_POST["idCommento"])."'");
	if (!$sqlCommento){
		//se commento non esiste
		exit;	
	}
	//controllo se chi sta eliminando è proprietario del commento o proprietario del blog
	if(isset($_SESSION["nomeUtente"])){
		$idPost = $sqlCommento["idPost"];
		$idBlog = getresult("SELECT * FROM post WHERE id = '".addslashes($idPost)."'")["idBlog"];
		$ownerBlog = getresult("SELECT * FROM partecipanti WHERE nomeUtente = '".addslashes($_SESSION["nomeUtente"])."'");
		//controllo se utente è proprietario del commento
		$ownerComment = getresult("SELECT * FROM commenti WHERE id = '".addslashes($_POST["idCommento"])."' AND autore = '".addslashes($_SESSION["nomeUtente"])."'");
		if(!$ownerComment && !$ownerBlog){
			//se utente non è proprietario né del blog né del commento
			exit;
		}
		//query di delete del commento superati i controlli
		$delComment = "DELETE FROM commenti WHERE id = '".addslashes($_POST["idCommento"])."'";
		$res = mysqli_query($link, $delComment);
		if(!$res){
			echo false;
		} else{
			echo true;	
		}
	} else {
		exit;
	}
}