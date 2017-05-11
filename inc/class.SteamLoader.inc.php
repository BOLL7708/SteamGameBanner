<?php 
class SteamLoader 
{
	const STATUS_USER_LOAD_FAILED = 0;
	const STATUS_USER_RESULT_EMPTY = 1;
	const STATUS_GAME_NOT_DEFINED = 2;
	const STATUS_GAME_IS_PREVIOUS = 3;
	const STATUS_GAME_NOT_FOUND = 4;
	const STATUS_GAME_IS_NEW = 5;

	public function getGameData($apiKey, $userId, $debug=false)
	{
		$fields	= $this->getFields();
		$prevId	= $this->getPreviousGameId();
		return $this->doRequest($apiKey, $userId, $fields, $prevId, $debug);
	}

	private function getFields()
	{
		$fields	= array_filter(explode(',', $_GET['fields'] ?? 'name,header_image'));
		if(!(is_array($fields) && count($fields) > 0)) $this->output('Missing game fields.', true);
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
		if(!$users) return $this->output('Loading user failed.', self::STATUS_USER_LOAD_FAILED);
		$users 		= json_decode($users);
		$userArr 	= $users->response->players ?? [];
		$user 		= array_pop($userArr);
		if(!$user) return $this->output('User not found or API returned nothing.', self::STATUS_USER_RESULT_EMPTY);

		// Game ID
		$gameId 	= $user->gameid ?? false;
		if(!$gameId) return $this->output('User is not playing a game right now.', self::STATUS_GAME_NOT_DEFINED, null);
		if($prevId && $prevId === $gameId) return $this->output('Not a new game, skipping.', self::STATUS_GAME_IS_PREVIOUS, $gameId);

		// Game
		$game 		= $this->getGame($gameId, $ch);
		if(!$game) return $this->output('Played game not found.', self::STATUS_GAME_NOT_FOUND);
		$game 		= json_decode($game);
		
		curl_close($ch);

		// Debug
		if($debug) {
			$gameOut = $game->$gameId->data;
			$gameOut->id = $gameId;
			return $this->output('Debug output.', self::STATUS_GAME_IS_NEW, $gameOut);
		}

		// Output
		$gameOut 		= new StdClass();
		$gameOut->id 	= $gameId;
		foreach($fields as $field) {
			$gameOut->$field = $game->$gameId->data->$field ?? null;
		}
		return $this->output('New game found.', self::STATUS_GAME_IS_NEW, $gameOut);
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

	private function output($message, $status=self::STATUS_GAME_IS_NEW, $data=null)
	{
		$output = ['message' => $message, 'status' => $status];
		if($data !== null) $output['game'] = $data;
		return $output;
	}
}
?>