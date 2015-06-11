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

				input[type=checkbox] {
				 -ms-transform: scale(2); /* IE */
				 -moz-transform: scale(2); /* FF */
				 -webkit-transform: scale(2); /* Safari and Chrome */
				 -o-transform: scale(2); /* Opera */
				 padding-left: 20px;
				}

        footer {
            color: #666;
            background-color: white;
            padding: 0px 0 0px 0;
            border-top: 1px solid #000;
				    position: absolute;
				    left: 0;
				    bottom: 0;
				    z-index: 5000;
				}
				footer .container {
            max-width: 100% !important;
         }

        #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -150px;
        }
        .push {
            height: 50px;
        }
        /* not required for sticky footer; just pushes hero down a bit */
        #wrap > .container {
            padding-top: 0px;
        }

        /* responsive footer fix by Aalaap Ghag */
				@media  {
            body {
                padding-left: 10px;
            }

            footer, #wrap {
                padding-left: 5px;
                padding-right: 0px;
            }

            #time {
            	height: 40px;
            }

			#time .ui-slider-handle {
				height: 45px;
				width: 45px;
			}
        }
/*
      .container {
				display: -webkit-box;      /* OLD - iOS 6-, Safari 3.1-6 */
				display: -moz-box;         /* OLD - Firefox 19- (buggy but mostly works) */
				display: -ms-flexbox;      /* TWEENER - IE 10 */
				display: -webkit-flex;     /* NEW - Chrome */
				display: flex;             /* NEW, Spec - Opera 12.1, Firefox 20+ */
/*
      }
*/
        /* end responsive footer fix */
		.rightPaneContainer{
			/* flex-grow:1; */
		}
		.Map {
			/* flex-grow:3; */
			border-radius: 10px;
			border: 2px solid #8AC007;
			margin:10px;
			padding-left: 5px;
			padding-right:5px;
			padding-top:5px;
			padding-bottom:5px;
			}
		.DatePickerContainer{
			border-radius: 10px;
			border: 2px solid #8AC007;
			margin:10px;
			max-width:300px;
			padding-left: 5px;
			padding-right: 5px;
			padding-top: 5px;
			padding-bottom: 5px;
			text-align: center;
			min-width: 200px;
		}
		#datepicker{
			max-width: 100px;
		}

		.EventList{
			margin:10px;
			border-radius: 10px;
			border: 2px solid #8AC007;
			height:500px;
			overflow-y: scroll;
			max-width:300px;
			min-width: 200px;
		}
		.Event {
			transition: background-color .5s ease ;
			margin: 3px;
			padding-left: 5px;
			padding-right: 5px;
			padding-top: 5px;
			padding-bottom: 5px;
			border-radius: 10px;
			border: 2px solid #000000;
			background: #000000;
			color: #FFFFFF;
		}

		.zooming{
			display: inline-block;
			padding-left: 10px;
			padding-top: 0px;
			margin-top: 0px;
		}
		#zoomCheckBox{
			padding-left: 10px;
			margin-right: 10px;
			margin-top: 0px;
			padding-top: 0px;
			text-align: top;
		}



    </style>

	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="scripts/jquery-ui-1.11.4.custom/jquery-ui.css">
	<script src="scripts/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.touch-punch.min.js"></script>


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
		<div class="Map span8">
			<object id="map-svg" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
		</div> <!-- /map -->
		<div class="rightPaneContainer span-4">
			<div class="DatePickerContainer"><input type="text" id="datepicker" onchange="newDate()"></div>
			<div class="EventList" id="EventList">	</div>
		</div> <!-- /rightPaneContainer -->
	</div> <!-- /container -->



					<footer>
						<div class="container">
								<div class="row"> <!-- Responsive 12 column grid -->
									<div class="span12"> <!-- all 12 columns for this row -->
												<input type="text" id="datepicker" onchange="newDate()">
												<div id="timeText">Time</div>
												<select id='modeSelect' onchange="modeChange()">
												<option value='live'>Live</option>
												<option value='manual'>Time Slider</option>
												</select>
												<div class="zooming"><input type="checkbox" id="zoomCheckBox" onchange="zoomBoxChanged()">Zooming</input></div>
									</div>
								</div>

								<div class="row"><!-- Responsive 12 column grid -->
									<div class="span12"> <!-- Using all 12 columns -->
										<div id="time"></div>
									</div> <!-- .span12 -->
							</div><!-- .row-fluid -->

						</div><!-- .span12 -->
					</div><!-- .row-fluid -->
				</div> <!-- .container -->
			</footer>
			<div id="mouseTooltip" >Event</div>
</div> <!-- end .wrap -->
<script src="js/bootstrap.min.js"></script>

<script src="scripts/events.js"></script>

</body>
</html>