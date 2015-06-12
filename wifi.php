<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="scripts/jquery-ui-1.11.4.custom/jquery-ui.css">
	<script src="scripts/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.touch-punch.min.js"></script>


<title>Campus WiFi Usage</title>
</head>
<body>
	<div id="wrap">
		<?php
			include 'menu.php';
		?>
		<div class="push"><!--//--></div>
		<!-- Begin page content -->
		<div class="container">
			<div>
				<object id="map-svg" width="100%" type="image/svg+xml" data="WifiMap.svg" onload="mapLoaded()"></object>
			</div>
			<br>
			<div id="test"></div>
			<div id="mouseTooltip" >stats</div>
		</div> <!-- end .container -->
	</div> <!-- end .wrap -->

<script src="js/bootstrap.min.js"></script>

<script src="scripts/wifi.js"></script>

</body>
</html>