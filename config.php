<?php
// See README.md for further instructions regarding API keys etc.
return [
	'apiKey'		=> '', 		// ~32 char hexadecimal, Steam Web API key
	'userId' 		=> '', 		// ~17 char integer, Steam User ID
	'intervalMs'	=> 30000, 	// milliseconds, between requests
	'debug'			=> false, 	// boolean, outputs messages to console
	'placeholder'	=> '',		// filename in /res/, background behind banner
	// I have noticed that the API will sometimes falsely report no game detected
	// so beware using the below option as it might cause alternation to no game
	'clearAtNoGame'	=> true 	// clears the game banner if no game is detected
];
?>