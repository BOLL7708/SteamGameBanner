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
		<style>
			body {
				background-color: black; 
				margin: 0; 
				padding: 0;
			}
			#banner {
				width: 100vw;
				height: 100vh;

				overflow: hidden;
				text-overflow: ellipsis;
			}
			body, #banner {
				background-size: cover;
				background-repeat: no-repeat;
				background-position: center;
			}
			#status {
				margin: 10px;
				font-size: 200%;
				font-family: sans-serif;
			 	color: white;					
			}
		</style>
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
				if(typeof data.error !== 'undefined') { // Error message
					$status.html(data.error);
					$banner.css('background-image', '');
					return;
				} 
				if(typeof data.message !== 'undefined') { // No need to update
					if(<?=Config::get("debug", false)?>) console.log(data.message);
					return; 
				}

				$banner.css('background-image', "url('"+data.header_image+"')");
				$status.html('');
				gameId = data.id;
			}

			function error(xhr, status, error) {
				console.log(error);
			}
		</script>
	</body>
</html>
