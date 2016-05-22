<?php
	require_once("config.php");
	set_time_limit(0);
	date_default_timezone_set(config::$default_time_zone);

	$method = $_GET["method"];

	require_once("controller/lib/general_functions.php");
	require_once("controller/lib/return_api_functions.php");
	require_once("controller/lib/validate_functions.php");
	require_once("controller/mastermind_controller.php");
	require_once("model/mastermind.php");

	$mastermind = new mastermind_controller();

	if (method_exists($mastermind, $method))
	{
		$mastermind->$method();
	}
	else
	{
		header("HTTP/1.1 405 ".return_api::status_http(405));
	}
?>