<?php 

class SteamLoader 
{
	public function getGameInfo($apiKey, $userId, $debug=false)
	{
		// print_r([$apiKey, $userId, $debug]);
		$fields 	= $this->getFields();
		$prevId 	= $this->getPreviousGameId();
		$game 		= $this->doRequest($apiKey, $userId, $fields, $prevId, $debug);
		return $game;
	}

	private function getFields()
	{
		$fields		= array_filter(explode(',', $_GET['fields'] ?? 'name,header_image'));
		if(!(is_array($fields) && count($fields) > 0)) 
			$this->output('Missing game fields.', true);
		return $fields;
	}

	private function getPreviousGameId()
	{
		return $_GET['previd'] ?? false;
	}

	private function doRequest($apiKey, $userId, $fields, $prevId, $debug)
	{
		// Init
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// User
		$users 		= $this->getUser($apiKey, $userId, $ch);
		if(!$users) 
			$this->output('Loading user failed.', true);
		$users 		= json_decode($users);
		$userArr 	= $users->response->players ?? [];
		$user 		= array_pop($userArr);
		if(!$user) 
			$this->output('User not found or API returned nothing.', true);

		// Game ID
		$gameId 	= $user->gameid ?? false; // '494830';
		if(!$gameId) 
			$this->output('User is not playing a game right now.', false, ($debug ? $user : null)); // number for debug, else false
		if($prevId && $prevId === $gameId)
			$this->output('Not a new game, skipping.', false);

		// Game
		$game 		= $this->getGame($gameId, $ch);
		if(!$game) 
			$this->output('Played game not found.', true);
		$game 		= json_decode($game);
		
		// Debug
		if($debug) {
			$gameOut = $game->$gameId->data;
			$gameOut->id = $gameId;
			return $gameOut;
		}

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

	private function output($message, $isError=false, $data=null)
	{
		header('Content-Type: application/json');
		$output = [($isError ? 'error' : 'message') => $message];
		if($data !== null) $output['data'] = $data;
		echo json_encode($output);
		exit;
	}
}

?>