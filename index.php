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
			#image {
				width: 100%;
				height: 100%;
				background-size: cover;
				background-repeat: no-repeat;
				background-position: center;
			 	white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			#label {
				margin: 10px;
				font-size: 200%;
				font-family: sans-serif;
			 	color: white;					
			}
		</style>
	</head>
	<body>
		<div id="image"><span id="label">No game detected yet...</span></div>
		<script type="text/javascript">

		var userId = ''; // http://steamidfinder.com/ Load profile, then click "Generate forum signature" and copy the "steamid" from the url.
		var gameId;
		var $image = $('#image:first');
		var $label = $('#label:first');

		update();
		setInterval(update, 10000);

		function update() 
		{
			$.ajax({
				url: 'data.php?id='+userId+(typeof gameId !== 'undefined' ? '&previd='+gameId : ''),
				success: success,
				error: error
			});
		}

		function success(data, status, xhr) {
			if(xhr.status != 200) return; // Failure to load data somewhere or it was a repeated request.
			if(typeof data.id !== 'undefined' && data.id === gameId) return; // Player is not in a game or it's the same game as shown.
			$image.css('background-image', "url('"+data.header_image+"')");
			$label.html(''); /* data.name */
			gameId = data.id;
		}

		function error(xhr, status, error) {
			console.log(error);
		}

		</script>
	</body>
</html>
