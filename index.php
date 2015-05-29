	<html>
	<head>
    <link href="css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <style type="text/css">

      /* Sticky footer styles
      -------------------------------------------------- */

      html,
      body {
        height: 100%;
        /* The html and body elements cannot have any padding or margin. */
      }

      /* Wrapper for page content to push down footer */
      #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0 auto -60px;
      }

      /* Set the fixed height of the footer here */
      #push,
      #footer {
        height: 140px;
      }
      #footer {
        background-color: #f5f5f5;
      }

      /* Lastly, apply responsive CSS fixes as necessary */
      @media (max-width: 767px) {
        #footer {
          margin-left: -20px;
          margin-right: -20px;
          padding-left: 20px;
          padding-right: 20px;
        }
      }



      /* Custom page CSS
      -------------------------------------------------- */
      /* Not required for template or sticky footer method. */

      #wrap > .container {
        padding-top: 60px;
      }
      .container .controls {
        margin: 20px 0;
      }

    </style>

	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="scripts/jquery-ui-1.11.4.custom/jquery-ui.css">
	<script src="scripts/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.touch-punch.min.js"></script>
	<script>
        var events;
		var currentTime;
		var liveRefreshInterval;
		var lastEventDataRefresh;

		function getPos(el) {
			for (var lx=0, ly=0;
			el != null;
			lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
			return {x: lx,y: ly};
		}

		function changeOpacity(SVGElement, newOpacity)
		{
			currentStyle = SVGElement.getAttributeNS(null, "style");
			if(currentStyle != null){
				currentStyle = currentStyle.replace(/;(\s|)Opacity:[^;]+(;|$)/i, ";");
				currentStyle = currentStyle.replace(/^(\s|)Opacity:[^;]+(;|$)/i, "");
				currentStyle = currentStyle.replace(/;(\s|)+$/i,"")
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";Opacity:"+newOpacity);
			} else{
				SVGElement.setAttributeNS(null, "style",  "Opacity:"+newOpacity);
			}
		}

		function changeFill(SVGElement, newFill)
		{
			currentStyle = SVGElement.getAttributeNS(null, "style");
			if(currentStyle != null){
				currentStyle = currentStyle.replace(/;(\s|)transition:[^;]+(;|$)/i, ";");
				currentStyle = currentStyle.replace(/^(\s|)transition:[^;]+(;|$)/i, "");
				currentStyle = currentStyle.replace(/;(\s|)+$/i,"")
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";transition: fill .4s");
			} else{
				SVGElement.setAttributeNS(null, "style",  "transition: fill .4s");
			}
			SVGElement.setAttributeNS(null, "style",  currentStyle + ";transition: fill .4s");


			currentStyle = SVGElement.getAttributeNS(null, "style");
			if(currentStyle != null){
				currentStyle = currentStyle.replace(/;(\s|)Fill:[^;]+(;|$)/i, ";");
				currentStyle = currentStyle.replace(/^(\s|)Fill:[^;]+(;|$)/i, "");
				currentStyle = currentStyle.replace(/;(\s|)+$/i,"")
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";Fill:"+newFill);
			} else{
				SVGElement.setAttributeNS(null, "style",  "Fill:"+newFill);
			}
			SVGElement.setAttributeNS(null, "style",  currentStyle + ";Fill:"+newFill);
		}

		function ignoreMouse(SVGElement)
		{
			currentStyle = SVGElement.getAttributeNS(null, "style");
			if(currentStyle != null){
				currentStyle = currentStyle.replace(/;(\s|)pointer-events:[^;]+(;|$)/i, ";");
				currentStyle = currentStyle.replace(/^(\s|)pointer-events:[^;]+(;|$)/i, "");
				currentStyle = currentStyle.replace(/;(\s|)+$/i,"")
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";pointer-events: none");
			} else{
				SVGElement.setAttributeNS(null, "style",  "pointer-events:none");
			}
			SVGElement.setAttributeNS(null, "style",  currentStyle + ";pointer-events:none");
		}

		function xinspect(o,i){
			if(typeof i=='undefined')i='';
			if(i.length>50)return '[MAX ITERATIONS]';
			var r=[];
			for(var p in o){
				var t=typeof o[p];
				r.push(i+'"'+p+'" ('+t+') => '+(t=='object' ? 'object:'+xinspect(o[p],i+'  ') : o[p]+''));
			}
			return r.join(i+'\n');
		}

		function fixObjectDates(element, index, array) {
			array[index].startTime = Number(element.startTime);
			array[index].endTime = Number(element.endTime);
			array[index].resources = JSON.parse(array[index].resources.replace(/ |\\|\/|\(|\)/g, '_'));
		}

		var resetStyles = function (){
            var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementsByTagName("*");
            for(var i=0; i < all.length; i++) {
                if (all[i].id != null && all[i].id.substring(0, 8) == "Resource") {
					changeOpacity(all[i],1);
					changeFill(all[i],"#aaaaaa");
					all[i].addEventListener("mousemove", mouseMove);
                    all[i].addEventListener("mouseleave", mouseLeave);
                }
				if (all[i].id != null && all[i].tagName.toLowerCase() == "text") {
					ignoreMouse(all[i]);
                }
            }
		}

		var mouseMove = function (e) {
			var mapPos = getPos(document.getElementById("map-svg"));
			document.getElementById("mouseTooltip").innerHTML = this.getAttributeNS(null, "customTooltip");
			document.getElementById("mouseTooltip").style.opacity = 1;
			document.getElementById("mouseTooltip").style.left = e.clientX + mapPos.x + 15;
			document.getElementById("mouseTooltip").style.top = e.clientY + mapPos.y + 15;
		}

		var mouseLeave = function () {
			document.getElementById("mouseTooltip").style.opacity = 0;
		}

		function sliderToCurrentTime(){
			var temp = new Date();
			temp.setHours(0);
			temp.setMinutes(0);
			temp.setSeconds(0);
			temp.setMilliseconds(0);

			$('#time').slider('value', (Number(new Date()) - Number(temp)) / 1000);

			document.getElementById("timeText").innerHTML  = new Date( Number(temp) + $("#time").slider("value")*1000).toLocaleTimeString();

			currentTime = Number(temp)/1000 + $("#time").slider("value");
		}

		function mapLoaded() {
			resetStyles();
			sliderToCurrentTime();
			getRooms();
			refreshMap();
			LiveMode();
			window.setInterval(eventDataRefreshCallback,30000);
		}

		$(function() {
			$( "#time" ).slider({
				orientation: "horizontal",
				min: 0,
				max: 86400,
				value: 36000,
				slide: sliderChange
				});
			$( "#time" ).slider( "value", 36000 );
			$("#time").draggable();
		});

		$( "#time" ).slider({
			range: false
		});

		function LiveMode(){
			document.getElementById('modeSelect').value = 'live';
			liveRefreshInterval = window.setInterval(LiveIntervalCallback,1000);
			sliderToCurrentTime();
			refreshMap();
		}

		function LiveIntervalCallback(){
			sliderToCurrentTime();
			refreshMap();
		}

		//Refresh event data every 5 minutes or after midnight
		function eventDataRefreshCallback(){
			var currentTime = new Date();
			if((Number(currentTime)-Number(lastEventDataRefresh)) > 5*60*1000 || (lastEventDataRefresh.getHours() == 23 && currentTime.getHours == 0)){
				getRooms();
			}
		}

		function manualMode(){
			document.getElementById('modeSelect').value = 'manual';
			window.clearInterval(liveRefreshInterval);
		}

		function modeChange(){
			if(document.getElementById('modeSelect').value == 'manual')
				manualMode();
			if(document.getElementById('modeSelect').value == 'live')
				LiveMode();
		}

		function sliderChange(){
			var temp = new Date();
			temp.setHours(0);
			temp.setMinutes(0);
			temp.setSeconds(0);
			temp.setMilliseconds(0);
			document.getElementById("timeText").innerHTML  = new Date( Number(temp) + $("#time").slider("value")*1000).toLocaleTimeString();

			currentTime = Number(temp)/1000 + $("#time").slider("value");

			refreshMap();

			manualMode();
		}

	   function refreshMap() {
			var svg = document.getElementById("map-svg");
			var svgDoc = svg.contentDocument;
			var all = svgDoc.getElementsByTagName("*");

			if(events == null)
				return;

			for (var i = 0; i < all.length; i++) {

				if (all[i].id == null || all[i].id.substring(0, 8) != "Resource"){
					continue;
				}

				var newFill = "#aaaaaa";
				var newTooltip = "";

				for(var j=0;j<events.length;j++)
				{
					if(events[j].startTime <= currentTime && events[j].endTime >= currentTime)
						for(var k=0;k<events[j].resources.length;k++)
							if (all[i].id != null && all[i].id.replace("Resource", "") == events[j].resources[k]){
								newFill = "#00ff00";
								newTooltip = events[j].name+"<br>"+(new Date( Number(events[j].startTime)*1000)).toLocaleTimeString()+" to "+(new Date( Number(events[j].endTime)*1000)).toLocaleTimeString();
							}
				}

				all[i].setAttributeNS(null, "customTooltip", newTooltip);
				changeOpacity(all[i],1);
				changeFill(all[i],newFill);
			}
		}

		function getRooms() {

			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var SerReturn = xmlhttp.responseText;
					var tempEvents= JSON.parse(SerReturn);
					tempEvents.forEach(fixObjectDates);
					events=tempEvents;
					refreshMap();
					lastEventDataRefresh=new Date();
				}
			}
			xmlhttp.open("GET", "scripts/getToday.php", true);
			xmlhttp.send();
		}
    </script>
	<title>Live Campus Map</title>
</head>
  <body>
		<?php
			include 'menu.php';
		?>
		<div>
			<object id="map-svg" width="100%" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
		</div>

		<div id="footer">
			<div class="containe-fluid">
			<div class="row-fluid">
					<div class="span1">
						<div id="timeText">10 AM</div>
					</div>
					<div class="span3">
						<select id='modeSelect' onchange="modeChange()"><option value='live'>Live</option><option value='manual'>Time Slider</option></select>
					</div>
				<div class="span8">
					<div id="time"></div>
					<div id="mouseTooltip" >Event</div>
				</div>
			</div>
		</div>

  <script src="js/bootstrap.min.js"></script>
  </body>
</html>
