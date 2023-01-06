<?php
require_once "config.php";
unset($_SESSION["idUtente"]);
unset($_SESSION["nomeUtente"]);
header("location:accesso.php");