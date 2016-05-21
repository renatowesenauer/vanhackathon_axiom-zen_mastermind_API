<?php
	set_time_limit(0);
	date_default_timezone_set("America/Sao_Paulo");

	$method = $_GET["method"];

	require_once("config.php");
	require_once("controller/mastermind_controller.php");
	require_once("model/mastermind.php");

	$mastermind = new mastermind_controller();

	if (method_exists($mastermind, $method))
		$mastermind->$method();
	else
		echo("Method does not exist");
?>