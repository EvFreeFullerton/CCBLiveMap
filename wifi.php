<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">	
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="scripts/jquery-ui-1.11.4.custom/jquery-ui.css">



<title>Campus WiFi Usage</title>
</head>
<body>
		<?php
			include 'menu.php';
		?>
<div class="container-fluid">
	<div id="wrap">
		<div class="container">
			<div>
				<object id="map-svg" class="img-responsive" type="image/svg+xml" data="WifiMap.svg" onload="mapLoaded()"></object>
			</div>
			<br>
			<div id="test"></div>
			<div id="mouseTooltip" >stats</div>
		</div> <!-- end .container -->
	</div> <!-- end .wrap -->
</div> <!-- end .container-fluid -->

	<script src="scripts/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.touch-punch.min.js"></script>

	<script src="js/bootstrap.min.js"></script>
	<script src="scripts/wifi.js"></script>
</body>
</html>