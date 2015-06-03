<html lang="en">
	<head>
    <link href="css/bootstrap.css" rel="stylesheet">

    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

     <style>
        html, body {
            height: 100%;
        }

				input[type=checkbox] {
				 -ms-transform: scale(2); /* IE */
				 -moz-transform: scale(2); /* FF */
				 -webkit-transform: scale(2); /* Safari and Chrome */
				 -o-transform: scale(2); /* Opera */
				}

        footer {
            color: #666;
            padding: 0px 0 0px 0;
            border-top: 1px solid #000;
				}
        #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -150px;
        }
        .push {
            height: 155px;
        }
        /* not required for sticky footer; just pushes hero down a bit */
        #wrap > .container {
            padding-top: 60px;
        }

        /* responsive footer fix by Aalaap Ghag */
				@media (max-width: 767px) {
            body {
                padding-right: 0px;
                padding-left: 0px;
            }

            footer, #wrap {
                padding-left: 20px;
                padding-right: 20px;
            }

            #time {
            	height: 60px;
            }
						#time .ui-slider-handle {
						  height: 65px;
						  width: 65px;
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
	<script src="scripts/events.js"></script>

	<title>Live Campus Map</title>
</head>
<body>
	<div id="wrap">
		<?php
			include 'menu.php';
		?>
      <!-- Begin page content -->
	<div class="container">
		<div>
			<object id="map-svg" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
		</div>
	</div>
  <div class="push"><!--//--></div>
</div> <!-- end .wrapper -->


					<footer>
						<div class="container">
								<div class="row-fluid"> <!-- Responsive 12 column grid -->
									<div class="span12"> <!-- all 12 columns for this row -->

										<div class="row-fluid"> <!-- make this row fluid -->
											<div class="span6"> <!-- this section will use 6 columns -->
														<div id="timeText">10 AM</div>
											</div><!-- .span6 -->
											<div class="span3"> <!-- this section is 3 columns -->
														<select id='modeSelect' onchange="modeChange()">
														<option value='live'>Live</option>
														<option value='manual'>Time Slider</option>
														</select>
											</div><!-- .span6 -->
											<div class="span3"> <!-- section is 3 columns -->
													<input type="checkbox" id="zoomCheckBox" checked="true" onchange="zoomBoxChanged()">Random Zooming</input>
											</div><!-- .span3 -->
										</div><!-- .row-fluid -->

								<div class="row-fluid"><!-- Responsive 12 column grid -->
									<div class="span12"> <!-- Using all 12 columns -->
											<div id="time"></div>
									</div> <!-- .span12 -->
							</div><!-- .row-fluid -->

						</div><!-- .span12 -->
					</div><!-- .row-fluid -->
				</div> <!-- .container -->
			</footer>
			<div id="mouseTooltip" >Event</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>