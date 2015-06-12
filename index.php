<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">	
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="scripts/jquery-ui-1.11.4.custom/jquery-ui.css">

<style type="text/css">
	body { 
/*		padding-top: 70px; 
		padding-bottom: 70px; */ 
		overflow-x: hidden; /* Prevent scroll on narrow devices */
	}
</style>

	<title>Live Campus Map</title>
</head>
<body>
	<?php
	include 'menu.php';
	?>

    <div class="container">
      <div class="row row-offcanvas row-offcanvas-right">
    
        <div class="col-xs-12 col-sm-8">
          <p class="pull-right visible-xs">
	            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle Events List</button>
	          </p>
				<object id="map-svg" class="img-responsive" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
			</div> <!-- /col-md-8 -->

			<div class="col-xs-6 col-sm-4 sidebar-offcanvas" id="sidebar">
				<div class="EventList" id="EventList"></div>
			</div> <!-- /col-md-4 -->

		</div> <!-- /row -->

	<div id="mouseTooltip" >Event</div>

	<nav class="navbar footer navbar-default navbar-fixed-bottom">
	  <div class="container">
		<div class="DatePickerContainer"><input type="text" id="datepicker" onchange="newDate()"></div>

		<div id="timeText">Time</div>
		<select id='modeSelect' onchange="modeChange()">
			<option value='live'>Live</option>
			<option value='manual'>Time Slider</option>
		</select>
		<div class="zooming"><input type="checkbox" id="zoomCheckBox" onchange="zoomBoxChanged()">Zooming</input></div>
		<div id="time"></div>
	  </div>
	</nav>
	</div> <!-- .containter -->

	<script src="scripts/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.touch-punch.min.js"></script>

	<script src="js/bootstrap.min.js"></script>
	<script src="scripts/events.js"></script>
</body>
</html>