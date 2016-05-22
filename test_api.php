<?php

	$method = $_GET["method"];

	if ($method == "new_game")
	{
	    $params = array("user_name" => $_GET["user"]);    
	    testAPI($method, $params);
	}

	if ($method == "guess")
	{
	    $params = array("game_key" => $_GET["gk"], 
	    	"user_name" =>  $_GET["user"],
	    	"colors" => explode(",", strtoupper($_GET["colors"])));    
	    testAPI($method, $params);
	}

	if ($method == "multiplayer")
	{
	    $params = array("game_key" => $_GET["gk"], 
	    	"user_name" => $_GET["user"]);    
	    testAPI($method, $params);
	}

	function testAPI($method, $params)
	{
	    $params_json = json_encode($params); 

	    $context = stream_context_create(array(
				    'http' => array(
				        'method' => 'POST',
				        'content' => $params_json,
				        'header' => "Content-type: application/x-www-form-urlencoded\r\n"
				        . "Content-Length: ".strlen($params_json)."\r\n"
				    )
				));

	    $data = file_get_contents("http://".$_SERVER["SERVER_NAME"]."/".$method, null, $context);

	    $data_array = json_decode($data, true);

	    if (is_array($data_array))
	    {
		    echo("<pre>");
		    print_r($data_array);
		    echo("</pre>");
		}
		else
		{
			echo($data);
		}
	}
?>	