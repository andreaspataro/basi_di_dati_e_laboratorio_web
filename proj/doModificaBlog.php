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

function controlli($titolo, $descrizione, $coautori, $argomenti, $link, $nomeUtente, $idBlog) {
	//se input hidden viene eliminato
	if(empty($idBlog)){
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//controllo se sei proprietario o coautore del blog
	$controllo = "SELECT * FROM partecipanti WHERE idBlog = '".addslashes($idBlog)."' AND nomeUtente = '".addslashes($nomeUtente)."'";
	$controlloRes = getresult($controllo);
	if(empty($controlloRes)){
		//non visualizzo l'errore con un alert perché si tratta di una manomissione
		header("location:javascript://history.go(-1)"); 
		return false;
	}
	//controllo dati se sono vuoti
	if(!$titolo || !$descrizione || !$argomenti) { //controllo inserimento campi
		header("location:gestoreErrori.php?idBlog=$idBlog&errore=campi&dest=modificaBlog");
		return false;
	}
	//controllo su lunghezza variabili
	if((strlen($titolo)>60)||(strlen($descrizione)>120)) {
		header("location:gestoreErrori.php?idBlog=$idBlog&errore=inputLength&dest=modificaBlog");
		return false;	
	}
	//controllo per ogni coautore immesso
	foreach($coautori as $i) {
		if(strlen($i)>30) { //controllo lunghezza nome utente
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=inputLength&dest=modificaBlog");
			return false;
		}
		//query di controllo se esiste nome utente in db
		$query = "SELECT * FROM utente WHERE nomeUtente = '".addslashes($i)."'";
		$risultato = mysqli_query($link,$query);
		$nomeTrovato = mysqli_fetch_assoc($risultato);
		if(!$nomeTrovato) { //se non esiste utente
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=coautore&valType=$i&dest=modificaBlog");
			return false;
		}
	}
	//controllo per ogni tema immesso
	foreach($argomenti as $k) {
		if(!$k){ //controllo inserimento campi
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=campi&dest=modificaBlog");
			return false;
		}
		if(strlen($k)>128) { //controllo lunghezza argomenti
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=inputLength&dest=modificaBlog");
			return false;
		}
		//validazione argomento
		if(preg_match("/^[a-zA-Z0-9]*(,[a-zA-Z0-9]+)*$/", $k)===0){
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=invalid&invalidType=$k&dest=modificaBlog");
			return false;	
		}
	}
	return true;
}

if (!isset($_SESSION["idUtente"])){
	//se utente non è loggato
	header("location:accesso.php");
	exit;
}

//raccolta dati
$idUtente = $_SESSION['idUtente']; //id utente corrente
$idBlog = $_POST['idBlog']; //id blog corrente
$nomeUtente = $_SESSION['nomeUtente']; //nome utente corrente
$titolo = $_POST["titolo"]; //titolo blog 
$descrizione = $_POST["descrizione"]; //descrizione blog
$coautoriString = $_POST["coautori"]; //stringa
$argomentiRaw = $_POST["argomenti"]; //array

//coautori da stringa a array ed elimino eventuali spazi
$coautoriExplode = array_map('trim',array_filter(explode(',',$coautoriString)));
$coautori = array_unique($coautoriExplode); //elimino duplicati nell'array
$argomentiTrim = []; //creo un array vuoto per contenere argomenti privi di spazi prima e dopo la virgola
foreach ($argomentiRaw as $x) { //elimino spazi per ogni elemento dell'array
	array_push($argomentiTrim, preg_replace('/\s*,\s*/', ',', $x));
} 
$argomenti = array_unique($argomentiTrim); //elimino duplicati nell'array

//chiamata funzione di controllo
$validazione = controlli($titolo, $descrizione, $coautori, $argomenti, $link, $nomeUtente, $idBlog);

//controllo successo validazione
if($validazione){
	//controlli premium
	if(isset($_POST["selection"]) && $_POST["selection"]=="premium"){ //solo se utente è premium
		//controllo se sono state selezionate le option
		$temaBlog = $_POST["temaBlog"]; //tema scelto
		$font = $_POST["font"]; //font scelto
		if(!$temaBlog || !$font){ //controllo inserimento campi
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=campi&dest=modificaBlog");
			exit;
		}
		$listaTemi = ['Tema Default','Tema Scuro','Tema Verde']; //temi consentiti
		$listaFont = ['Roboto (default)','Open Sans','Lato','Noto Sans JP']; //font consentiti
		//controlli manomissione codice
		if(!in_array($temaBlog, $listaTemi)){ //se tema inserito non appartiene a temi consentiti 
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=hack&dest=modificaBlog");
			exit;
		}
		if(!in_array($font, $listaFont)){ //se font inserito non appartiene a font consentiti
			header("location:gestoreErrori.php?idBlog=$idBlog&errore=hack&dest=modificaBlog");
			exit;
		}
	}
	//query update dati in tabella blog
	$stmt = $link->prepare("UPDATE blog SET nomeBlog = ?, descrizione = ? WHERE id = '$idBlog'");
	$stmt->bind_param('ss', $titolo, $descrizione);
	$result = $stmt->execute();
	if(!$result) { //errore query
		echo "Errore: " . mysqli_error($link);
		exit;
	} else {
		//query inserimento dati in tabella partecipanti
		//ricavo autore blog
		$autore = getresult("SELECT autore FROM blog WHERE id = $idBlog")["autore"]; 
		//rimuovo i coautori precedenti
		$sqlRemove1 = "DELETE FROM partecipanti WHERE idBlog = $idBlog AND nomeUtente != '".addslashes($autore)."'";
		$resultRemove1 = mysqli_query($link, $sqlRemove1);
		if(!$resultRemove1){
			echo "Error deleting record: " . mysqli_error($link);
			exit;
		}
		//inserisco coautori
		$stmt2 = $link->prepare("INSERT INTO partecipanti (nomeUtente, idBlog)
			VALUES (?,?)"); 
		foreach ($coautori as $i) {
			$stmt2->bind_param('si', $i, $idBlog);
			$result3 = $stmt2->execute();
			if(!$result3) { //errore query
				echo "Errore: " . mysqli_error($link);
				exit;
			}
		}
		//query inserimento dati in tabella argomenti
		//rimuovo gli argomenti e sottoargomenti precedenti
		$sqlRemove2 = "DELETE FROM argomentiblog WHERE idBlog = '".addslashes($idBlog)."'";
		$resultRemove2 = mysqli_query($link, $sqlRemove2);
		if(!$resultRemove2){
			echo "Error deleting record: " . mysqli_error($link);
			exit;
		}
		$argomentiExplode = []; //array vuoto per divisione dove occorre virgola tra argomento e sottoargomento
		$argomentoIndex = 0; //indice zero per gli argomenti
		foreach ($argomenti as $k) {
			array_push($argomentiExplode, preg_split("/[,]+/", $k)); //split dove occorre la virgola
		}
		for ($i=0;$i<sizeof($argomentiExplode);$i++){ //argomenti
			//vedo se esiste argomento
			$checkArg = "SELECT * FROM argomenti WHERE argomento = '".addslashes($argomentiExplode[$i][0])."' AND idParent = 0";
			$resCheckArg = mysqli_query($link,$checkArg);
			$existentArg = mysqli_fetch_assoc($resCheckArg);
			if(!$existentArg){
				//query inserimento argomenti in tabella argomenti
				$queryArg = $link->prepare("INSERT INTO argomenti (idParent, argomento)
				VALUES (?,?)");
				$queryArg->bind_param('is', $argomentoIndex, $argomentiExplode[$i][0]);
				$resultArg = $queryArg->execute();
				if(!$resultArg) { //errore query
					echo "Errore1: " . mysqli_error($link);
					exit;
				}
				$argomentoId = mysqli_insert_id($link);	//prendo l'id dell'ultimo argomento inserito
			} else {
				$argomentoId = $existentArg["id"];
			}
			//query inserimento id dell' ultimo argomento inserito in tabella relazione argomentiblog
			$queryBlogArg = $link->prepare("INSERT INTO argomentiblog (idArgomenti, idBlog)
				VALUES (?,?)");
			$queryBlogArg->bind_param('ii', $argomentoId, $idBlog);
			$resultBlogArg = $queryBlogArg->execute();
			if(!$resultBlogArg) { //errore query
				echo "Errore2: " . mysqli_error($link);
				exit;
			}
			if(isset($argomentiExplode[$i][1])){ //sottoargomenti
				//vedo se esiste sottoargomento che punta allo stesso idParent
				$checkSub = "SELECT * FROM argomenti WHERE argomento = '".addslashes($argomentiExplode[$i][1])."' AND idParent = '".addslashes($argomentoId)."'";
				$resCheckSub = mysqli_query($link,$checkSub);
				$existentSub = mysqli_fetch_assoc($resCheckSub);
				if(!$existentSub){
					//query inserimento sottoargomenti in tabella argomenti 
					$querySottoArg = $link->prepare("INSERT INTO argomenti (idParent, argomento)
					VALUES (?,?)");
					$querySottoArg->bind_param('is', $argomentoId, $argomentiExplode[$i][1]);
					$resultSottoArg = $querySottoArg->execute();
					if(!$resultSottoArg) { //errore query
						echo "Errore3: " . mysqli_error($link);
						exit;
					}
					$sottoArgomentoId = mysqli_insert_id($link); //prendo l'id dell'ultimo sottoargomento inserito 	
				} else {
					$sottoArgomentoId = $existentSub["id"];
				}
				//query inserimento id dell' ultimo sottoargomento inserito in tabella relazione argomentiblog
				$queryBlogSottoArg = $link->prepare("INSERT INTO argomentiblog (idArgomenti, idBlog)
					VALUES (?,?)");
				$queryBlogSottoArg->bind_param('ii', $sottoArgomentoId, $idBlog);
				$resultBlogSottoArg = $queryBlogSottoArg->execute();
				if(!$resultBlogSottoArg) { //errore query
					echo "Errore4: " . mysqli_error($link);
					exit;
				}
			}	
		}
		if(isset($_POST["selection"]) && $_POST["selection"]=="premium"){ //solo se utente è premium
			//update tabella personalizzazione di tema e font
			$sqlRemove3 = "DELETE FROM personalizzazione WHERE idBlog = '".addslashes($idBlog)."'";
			$resultRemove3 = mysqli_query($link, $sqlRemove3);
			if(!$resultRemove3){
				echo "Error deleting record: " . mysqli_error($link);
				exit;
			}
			$stmt3 = $link->prepare("INSERT INTO personalizzazione (idBlog, temaBlog, fontBlog) VALUES (?,?,?)");
			$stmt3->bind_param('iss', $idBlog, $temaBlog, $font);
			$result3 = $stmt3->execute();
			if(!$result3){ //errore query
				echo "Errore: " . mysqli_error($link);
				exit;
			}	
		}
	}
	//se tutto ha successo
	header("location:blog.php?idBlog=$idBlog");
}