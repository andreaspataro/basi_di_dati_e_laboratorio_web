<?php
//pagina di gestione degli errori
$destinazione = htmlentities($_GET["dest"]); //destinazione del redirect
?>
<form name="form" id="form" method="post" action="<?=$destinazione?>.php">
	<?php
	foreach ($_GET as $i => $v) {
		//creo input hidden in base ai dati passati
		echo '<input type="hidden" name="'.htmlentities($i).'" value="'.htmlentities($v).'">';
	}
	?>
</form>
<script>
	//submit della form 
	window.onload = submitForm;
	function submitForm(){
		document.getElementById("form").submit();
	}
</script>