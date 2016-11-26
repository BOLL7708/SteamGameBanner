<?php

$sl = new SteamLoader();
$sl->getGameInfo();

class SteamLoader 
{
	const APIKEY = ''; // https://steamcommunity.com/dev/apikey

	public function getGameInfo()
	{
		$steamId 	= $this->getSteamId();
		$fields 	= $this->getFields();
		$prevId 	= $this->getPreviousGameId();
		$game 		= $this->doRequest($steamId, $fields, $prevId);

		header('Content-Type: application/json');
		echo json_encode($game);
	}

	private function getSteamId() 
	{
		$steamId 	= $_GET['id'] ?? false;
		if(!$steamId) 
			$this->outputError(204, 'User ID not supplied.');
		return $steamId;
	}

	private function getFields()
	{
		$fields		= array_filter(explode(',', $_GET['fields'] ?? 'name,header_image'));
		if(!(is_array($fields) && count($fields) > 0)) 
			$this->outputError(204, 'Missing game fields.'); // Broken
		return $fields;
	}

	private function getPreviousGameId()
	{
		return $_GET['previd'] ?? false;
	}

	private function doRequest($steamId, $fields, $prevId)
	{
		// Init
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// User
		$users 		= $this->getUser(self::APIKEY, $steamId, $ch);
		if(!$users) 
			$this->outputError(204, 'Getting user failed.');
		$users 		= json_decode($users);
		$userArr 	= $users->response->players ?? [];
		$user 		= array_pop($userArr);
		if(!$user) 
			$this->outputError(204, 'User was not found.');

		// Game ID
		$gameId 	= $user->gameid ?? false; // '494830';
		if(!$gameId) 
			$this->outputError(204, 'User is not playing a game right now.'); // number for debug, else false
			// Save gameID to cookie to save on requests, if still same game, no update.
		if($prevId && $prevId === $gameId)
			$this->outputError(204, 'Not a new game, skipping.');

		// Game
		$game 		= $this->getGame($gameId, $ch);
		if(!$game) 
			$this->outputError(204, 'Played game was not found.');
		$game 		= json_decode($game);
			
		// Output
		$gameOut 	= new StdClass();
		$gameOut->id = $gameId;
		foreach($fields as $field) {
			$gameOut->$field = $game->$gameId->data->$field ?? null;
		}
		curl_close($ch);
		return $gameOut;
	}

	private function getUser($key, $steamId, $ch) 
	{
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$key&steamids=$steamId";
		curl_setopt($ch, CURLOPT_URL, $url);
		return curl_exec($ch);
	}

	private function getGame($gameId, $ch)
	{
		$url = "http://store.steampowered.com/api/appdetails?appids=$gameId";
		curl_setopt($ch, CURLOPT_URL, $url);
		return curl_exec($ch);
	}

	private function outputError($code, $message)
	{
		http_response_code($code);
		exit($message);
	}
}

?>