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
	<link rel="stylesheet" href="style.css">
	<script>
		var wifiData;

		function getPos(el) {
			for (var lx=0, ly=0;
			el != null;
			lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
			return {x: lx,y: ly};
		}

		function elementMouseOver(e){
			var mapPos = getPos(document.getElementById("map-svg"));
			var temp = this.getAttributeNS(null, "temperature");
			document.getElementById("mouseTooltip").innerHTML = temp;
			document.getElementById("mouseTooltip").style.opacity = 1;
			document.getElementById("mouseTooltip").style.left = e.clientX + mapPos.x + 15;
			document.getElementById("mouseTooltip").style.top = e.clientY + mapPos.y + 15;
		}

		function mouseLeave(){
			document.getElementById("mouseTooltip").style.opacity = 0;
		}

		function changeFill(SVGElement, newFill)
		{
			currentStyle = SVGElement.getAttributeNS(null, "style");
			if(currentStyle != null){
				currentStyle = currentStyle.replace(/;(\s|)transition:[^;]+(;|$)/i, ";");
				currentStyle = currentStyle.replace(/^(\s|)transition:[^;]+(;|$)/i, "");
				currentStyle = currentStyle.replace(/;(\s|)+$/i,"")
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";transition: fill 1s");
			} else{
				SVGElement.setAttributeNS(null, "style",  "transition: fill 1s");
			}
			SVGElement.setAttributeNS(null, "style",  currentStyle + ";transition: fill 1s");

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
				SVGElement.setAttributeNS(null, "style",  currentStyle + ";pointer-events:none");
			} else{
				SVGElement.setAttributeNS(null, "style",  "pointer-events:none");
			}
			SVGElement.setAttributeNS(null, "style",  currentStyle + ";pointer-events:none");
		}


		var resetStyles = function (){
            var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementsByTagName("*");
            for(var i=0; i < all.length; i++) {
                if (all[i].id != null && all[i].id.substring(0, 8) == "Resource") {
					changeFill(all[i],"#aaaaaa");
                }
				if (all[i].id != null && all[i].tagName.toLowerCase() == "text") {
					ignoreMouse(all[i]);
                }
            }
		}

		function changeElementColor(name, color, temperature){
		     var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementsByTagName("*");
            for(var i=0; i < all.length; i++) {
                if (all[i].id != null && all[i].id.substring(0, 8) == "Resource") {
					if(all[i].id == name){
						changeFill(all[i],color);
						all[i].setAttributeNS(null, "temperature",  temperature);
						}
                }
            }
		}

		function changeElementText(name, textV){
		     var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementById(name);
			all.textContent= textV;
		}

		function mapLoaded() {
			resetStyles();
			getRooms();
		}

		function getTemp(area)
		{
			for(var i=0;i<thermostatData.length; i++){
				if(thermostatData[i].name == area)
					return thermostatData[i].currentTemp;
			}
			return 0;
		}

		function componentToHex(c) {
			var hex = c.toString(16);
			return hex.length == 1 ? "0" + hex : hex;
		}

		function setupRoom(resource, thermostat){
			var temperature = getTemp(thermostat);
			changeElementColor(resource, tempToColor(temperature),temperature);
			var svg = document.getElementById("map-svg");
			var svgDoc = svg.contentDocument;
			element = svgDoc.getElementById(resource);
			element.addEventListener("mousemove",elementMouseOver);
			element.addEventListener("mouseleave",mouseLeave);
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
					wifiData = JSON.parse(SerReturn);

					changeElementText("Wifi1",wifiData[0].totalUsers);

				}
			}
			xmlhttp.open("GET", "scripts/getWifi.php", true);
			xmlhttp.send();
		}
    </script>
<title>Campus WiFi Usage</title></head>
  <body>
<?php
	include 'menu.php';
?>
	<div><object id="map-svg" title="HellO" width="100%" type="image/svg+xml" data="WifiMap.svg" onload="mapLoaded()"></object></div><br>
	<a href="index.html">Events Map</a>
	<div id="test"></div>
  </body>

<div id="mouseTooltip" >Temperature</div>

</html>
