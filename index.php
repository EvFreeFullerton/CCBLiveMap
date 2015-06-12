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

<style type="text/css">
	body { 
/*		padding-top: 70px; 
		padding-bottom: 70px; */ 
	}
</style>

	<title>Live Campus Map</title>
</head>
<body>
	<div id="wrap">
		<?php
		include 'menu.php';
		?>
		<div class="push"><!--//--></div>
		<!-- Begin page content -->
		<div class="container">
			<div class="Map col-md-8">
				<object id="map-svg" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
			</div> <!-- /map -->
			<div class="rightPaneContainer col-md-4">
				<div class="DatePickerContainer"><input type="text" id="datepicker" onchange="newDate()"></div>
				<div class="EventList" id="EventList">	</div>
			</div> <!-- /rightPaneContainer -->
		</div> <!-- /container -->





	<nav class="navbar navbar-default navbar-fixed-bottom">
	  <div class="container">

		<input type="text" id="datepicker" onchange="newDate()">
		<div id="timeText">Time</div>
		<select id='modeSelect' onchange="modeChange()">
			<option value='live'>Live</option>
			<option value='manual'>Time Slider</option>
		</select>
		<div class="zooming"><input type="checkbox" id="zoomCheckBox" onchange="zoomBoxChanged()">Zooming</input></div>
		<div id="time"></div>
	  </div>
	</nav>


	<div id="mouseTooltip" >Event</div>
</div> <!-- end .wrap -->

<script src="js/bootstrap.min.js"></script>
<script src="scripts/events.js"></script>
</body>
</html>