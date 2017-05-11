<?php
function __autoload($class_name)
{
	include_once 'inc/class.' . $class_name . '.inc.php';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="styles.css">
	</head>
	<body>
		<div id="banner"><span id="status">No game detected yet...</span></div>
		<script type="text/javascript">
			var gameId;
			var $banner = $('#banner:first');
			var $status = $('#status:first');

			// Put placeholder banner in the background.
			var placeholder = '<?=Config::print("placeholder", "")?>';
			if(placeholder !== '') {
				$('body:first').css('background-image', 'url(res/'+placeholder+')');
				$status.html('');
			}

			// Run update
			update();
			setInterval(update, '<?=Config::print("intervalMs", 30000)?>');

			function update() 
			{
				$.ajax({
					url: 'data.php'+(typeof gameId !== 'undefined' ? '?previd='+gameId : ''),
					success: success,
					error: error
				});
			}

			function success(data, status, xhr) {
				switch(data.status) {
					case <?=SteamLoader::STATUS_USER_LOAD_FAILED?>:
						clearBanner(data.message);
						break;
					case <?=SteamLoader::STATUS_USER_RESULT_EMPTY?>:
						clearBanner(data.message);
						break;
					case <?=SteamLoader::STATUS_GAME_NOT_DEFINED?>:
						// This means we are not playing, if not clearing we leave previous game up.
						if(<?=Config::get("clearAtNoGame", true) ? 'true' : 'false'?>) clearBanner("");
						gameId = undefined;
						break;
					case <?=SteamLoader::STATUS_GAME_IS_PREVIOUS?>:
						// Do nothing
						break;
					case <?=SteamLoader::STATUS_GAME_NOT_FOUND?>:
						clearBanner(data.message);
						break;
					case <?=SteamLoader::STATUS_GAME_IS_NEW?>:
						$banner.css('background-image', "url('"+data.game.header_image+"')");
						$status.html('');
						gameId = data.game.id;
						break;
				}
				if(<?=Config::get("debug", false) ? 'true' : 'false'?>) console.log(data.message);

				function clearBanner(message) {
					$status.html(message);
					$banner.css('background-image', '');
				}
			}

			function error(xhr, status, error) {
				console.log(error);
			}
		</script>
	</body>
</html>
