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
$postId = '';

if(isset($_POST["idBlog"]) && isset($_POST["lastPostId"])){
	//controllo se idBlog esiste
	$sqlBlog = getresult("SELECT * FROM blog WHERE id = '".addslashes($_POST["idBlog"])."'");
	if (!$sqlBlog){
		//se blog non esiste
		exit;	
	}
	//estrazione stile blog
	$idBlog = $sqlBlog["id"];
	$tema = getresult("SELECT temaBlog FROM personalizzazione WHERE idBlog = '".addslashes($idBlog)."'");
	if(isset($tema)){
		$blogStyle = $tema["temaBlog"];
	}
	$sql = "SELECT * FROM post WHERE idBlog = '".addslashes($_POST["idBlog"])."' AND id < '".addslashes($_POST["lastPostId"])."' ORDER BY dataCreazione DESC LIMIT 3";
	$result = mysqli_query($link, $sql);
	if(mysqli_num_rows($result) > 0){
		while($post = mysqli_fetch_assoc($result)){
			$postId = $post["id"];	
			$output .= '<div class="card ';
			if(isset($blogStyle)){
				if($blogStyle == 'Tema Scuro'){
					$output .= 'neroCards';
				}
				if($blogStyle == 'Tema Verde'){
					$output .= 'verdeCards';
				}
			}
			$output .= '">';
			//estrazione immagini del post se esistono
			$postPics = "SELECT directory FROM immagini WHERE idPost = '".addslashes($postId)."'";
			$picsRes = mysqli_query($link, $postPics);
			$pics = [];
			while($picsRow = mysqli_fetch_assoc($picsRes)){
				$pics[] = htmlentities($picsRow["directory"]);
			}
			if(!empty($pics)){ 
			//se esistono foto per questo post
				$output .= '<div class="postImage ';
				if(isset($blogStyle)){
					if($blogStyle == 'Tema Scuro'){
						$output .= 'neroCardsTitle';
					}
					if($blogStyle == 'Tema Verde'){
						$output .= 'verdeCardsTitle';
					}
				}
				$output .= '">';
				//prendo solo la prima foto per la preview
				$output .= '<img src = "'.$pics[0].'" alt="Immagine di preview post" class="dirImage">';  
				$output .= '</div>';
			}
			$output .= '<div class="cardBody">';
			$output .= '<h2 class="postTitle">'.htmlentities($post["titolo"]).'</h2>';//titolo
			$output .= '<p class="postText">';
			$postPreview = htmlentities($post["contenuto"]);//contenuto
			if(strlen($postPreview)>70){ //preview del contenuto del post
				$corta = substr($postPreview, 0, 70);
				$output .= $corta.'...</p>';
			} else {
				$output .= $postPreview.'</p>';  
			}
			$output .= '<button class="button white post ';
			if(isset($blogStyle)){
				if($blogStyle == 'Tema Scuro'){
					$output .= 'neroButtons';
				}
			} 
			$output .= '"onclick="window.location.href=\'';
			$output .= 'post.php?idBlog='.htmlentities($idBlog).'&idPost='.htmlentities($postId).'\'">Continua a Leggere</button>'; //bottone per leggere il post
			if (isset($row)){
				$output .= '<button class="button grey ';
				if(isset($blogStyle)){
					if($blogStyle == 'Tema Scuro'){
					$output .= 'neroButtons2';
					}
				}
				$output .= '"style="float: right;" onclick="riempiFormModificaPost('.htmlentities($postId).')">Modifica Post</button>'; //bottone per modificare il post se ne hai diritto
			}
			$output .= '</div>';
			$output .= '<div class="cardFooter '; 
			if(isset($blogStyle)){
				if($blogStyle == 'Tema Scuro'){
					$output .= 'neroCardsFooter';
				}
				if($blogStyle == 'Tema Verde'){
					$output .= 'verdeCardsFooter';
				}
			}
			$date = htmlentities($post["dataCreazione"]); //data creazione del post
			$timestamp = strtotime($date);
			$data = date('d/m/Y', $timestamp);
			$ora = date('H:i', $timestamp);
			//dati creazione post
			$output .= '"> Scritto da '.htmlentities($post["autore"]).' il '.$data.' alle '.$ora.'</div>';
			$output .= '</div>' ;
		}
		$output .= '
			<div id="removeRow">
			<button class="button blue long';
		$output .='"name="buttonMore" id="buttonMore" data-post="'.addslashes(htmlentities($postId)).'">Mostra post precedenti</button></div>';
		echo $output;
	}
}