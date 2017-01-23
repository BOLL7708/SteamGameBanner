<?php

// See README.md for further instructions regarding API keys etc.

return [
	'apiKey'		=> '', 		// ~32 char hexadecimal, Steam Web API key
	'userId' 		=> '', 		// ~17 char integer, Steam User ID
	'intervalMs'	=> 30000, 	// milliseconds, between requests
	'debug'			=> false, 	// boolean, outputs messages to console
	'placeholder'	=> '',		// filename in /res/, background behind banner
	'clearAtNoGame'	=> true 	// clears the game banner if not game is detected
];

?>