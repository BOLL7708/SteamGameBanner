<?php

function __autoload($class_name)
{
	include_once 'inc/class.' . $class_name . '.inc.php';
}

$sl = new SteamLoader();
$game = $sl->getGameInfo(
	Config::get('apiKey'), 
	Config::get('userId'), 
	Config::get('debug', false)
);

header('Content-Type: application/json');
echo json_encode($game);

?>