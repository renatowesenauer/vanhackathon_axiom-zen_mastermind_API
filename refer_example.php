<?php

	$method = $_GET["method"];

	if ($method == "new_game")
	{

	    $params = array("user_name" => "renato");    
	    $params_json = json_encode($params); 

	    $context = stream_context_create(array(
				    'http' => array(
				        'method' => 'POST',
				        'content' => $params_json,
				        'header' => "Content-type: application/x-www-form-urlencoded\r\n"
				        . "Content-Length: ".strlen($params_json)."\r\n"
				    )
				));

	    $data = file_get_contents("http://".$_SERVER["SERVER_NAME"]."/new_game", null, $context);

	    echo("<pre>");
	    print_r(json_decode($data, true));
	    echo("</pre>");
	}

	if ($method == "guess")
	{

	    $params = array("game_key" => $_GET["gk"], 
	    	"user_name" =>  "rmododo",
	    	"colors" => explode(",", strtoupper($_GET["colors"])));    
	    $params_json = json_encode($params); 

	    $context = stream_context_create(array(
				    'http' => array(
				        'method' => 'POST',
				        'content' => $params_json,
				        'header' => "Content-type: application/x-www-form-urlencoded\r\n"
				        . "Content-Length: ".strlen($params_json)."\r\n"
				    )
				));

	    $data = file_get_contents("http://".$_SERVER["SERVER_NAME"]."/guess", null, $context);

	    echo($data);

	    echo("<pre>");
	    print_r(json_decode($data, true));
	    echo("</pre>");
	}

	if ($method == "multiplayer")
	{

	    $params = array("game_key" => $_GET["gk"], 
	    	"user_name" => "rmododo");    
	    $params_json = json_encode($params); 

	    $context = stream_context_create(array(
				    'http' => array(
				        'method' => 'POST',
				        'content' => $params_json,
				        'header' => "Content-type: application/x-www-form-urlencoded\r\n"
				        . "Content-Length: ".strlen($params_json)."\r\n"
				    )
				));

	    $data = file_get_contents("http://".$_SERVER["SERVER_NAME"]."/multiplayer", null, $context);

	    echo( $data);

	    echo("<pre>");
	    print_r(json_decode($data, true));
	    echo("</pre>");
	}
?>	