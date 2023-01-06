<?php
$link = mysqli_connect($DBhost, $DBuser, $Dbpassword, $Dbname);
if (!$link) {
	die ('Non riesco a connettermi: '. mysqli_error($link));
}