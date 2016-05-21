<?php

    $params = array("user_name" => "jose_silva");    
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
?>	