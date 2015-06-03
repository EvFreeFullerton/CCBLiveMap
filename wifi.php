<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

      <style>
        html, body {
            height: 100%;
        }

        #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -150px;
        }

        /* responsive footer fix by Aalaap Ghag */
				@media (max-width: 767px) {
            body {
                padding-left: 10px;
            }

            #wrap {
                padding-left: 5px;
                padding-right: 0px;
            }
        }

        .container {
            max-width: 940px;
        }
        /* end responsive footer fix */
    </style>



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