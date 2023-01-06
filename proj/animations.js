function deleteAccount(){
	//funzione che mostra un pop-up di conferma per l'eliminazione dell'account
	try	{
		document.getElementById('all').style="-webkit-filter: blur(8px); -o-filter: blur(8px);";
		document.body.style="margin: 0; height: 100%; overflow: hidden;"
		document.getElementById('deleteAccount').style="display: block;";
	} catch (error) {
		console.error(error);
	}
}

function closeDeleteAccount(){
	//funzione che chiude il pop-up di conferma per l'eliminazione dell'account
	try {
		document.getElementById('all').style="-webkit-filter: blur(0px); -o-filter: blur(0px);";
		document.body.style="margin: 0; height: 100%; overflow: none;"
		document.getElementById('deleteAccount').style="display: none;";	
	} catch (error) {
		console.error(error);
	} 
}

function deleteBlog(){
	//funzione che mostra un pop-up di conferma per l'eliminazione del blog
	try {
		document.getElementById('all2').style="-webkit-filter: blur(8px); -o-filter: blur(8px);";
		document.body.style="margin: 0; height: 100%; overflow: hidden;"
		document.getElementById('deleteAccount').style="display: block;";	
	} catch (error) {
		console.error(error);
	} 
}

function closeDeleteBlog(){
	try {
		//funzione che chiude il pop-up di conferma per l'eliminazione del blog
		document.getElementById('all2').style="-webkit-filter: blur(0px); -o-filter: blur(0px);";
		document.body.style="margin: 0; height: 100%; overflow: none;"
		document.getElementById('deleteAccount').style="display: none;";	
	} catch (error) {
		console.error(error);
	} 
}


function photoUpload(){
	//funzione che mostra le foto selezionate al momento della creazione del post 
	try {
		var x = document.getElementById('file');
		var txt = "";
		if ('files' in x){ //se ci sono file caricati
			if (x.files.length >= 1){
				if (x.files.length > 2){ //limite di 2 immagini per post
					txt = "Puoi aggiungere al massimo due immagini";
					x.value = "";
				}
				for (var i = 0; i < x.files.length; i++){
					//scorro le immagini
					var file = x.files[i];
					//ricavo il formato di ogni immagine 
					var a = file["name"].split(/\.(?=[^\.]+$)/)[1];
					if(a=="jpg"||a=="jpeg"||a=="png"||a=="jfif"){ 
						//se formato dell'immagine è accettato
						if ('name' in file) { 
							if (i == x.files.length - 1){
								//se è l'ultima immagine non aggiungo la virgola
								txt += file.name	;
							} else {
								//aggiungo la virgola
								txt += file.name + ", ";
							}
						}		
					} else {
						//se formato dell'immagine non è accettato
						txt = "Puoi aggiungere solo immagini";
						x.value = "";	
					}
					//controllo grandezza immagini 500kb max
					if(file.size>500000){
						txt = "Ogni immagine può essere grande al massimo 500kb";
						x.value = "";		
					}
				}
			} else {
				//se non ci sono file caricati
				txt = "Nessuna immagine scelta";
			}
		}
		//mostro i nomi delle immagini selezionate
		document.getElementById("labelPhoto").innerHTML = txt;
		console.log(x.files);	
	} catch (error) {
		console.error(error);
	} 
}

function aggiungiTema(){
	//funzione che aggiunge un input per gli argomenti alla form di creazione o modifica del blog 
	try {
		var input = document.createElement("INPUT");
		var button = document.getElementById("removeTemi");
		input.name = "argomenti[]";
		input.className = "input lunghi";
		input.setAttribute("placeholder","Inserisci Argomento e Sottoargomento (opzionale) separati da una virgola ','");
		input.required = true;
		input.maxLength = "128";
		input.type = "text";
		input.style="margin-top: 5px;"
		document.getElementById("divTemi").appendChild(input);
		button.style="display: inline;";	
	} catch (error) {
		console.error(error);
	} 
}

function rimuoviTema(){
	//funzione che rimuove un input per gli argomenti alla form di creazione o modifica del blog
	try {
		var divTemi = document.getElementById("divTemi");
		var button = document.getElementById("removeTemi");
		if (divTemi.childElementCount == 1) { 
		//se c'è solo un input non posso rimuovere
			return;
		} else if (divTemi.childElementCount == 2){ 
		//se ci sono due input rimuovo l'ultimo e nascondo il bottone per rimuovere
			button.style="display: none;";
			divTemi.removeChild(divTemi.lastChild);
		} else {
			divTemi.removeChild(divTemi.lastChild);
		}	
	} catch (error) {
		console.error(error);
	} 
}