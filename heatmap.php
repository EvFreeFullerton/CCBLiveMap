<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

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


      /* Custom page CSS
      -------------------------------------------------- */
      /* Not required for template or sticky footer method. */

      .container {
        width: auto;
        max-width: 680px;
      }
      .container .controls {
        margin: 20px 0;
      }

    </style>

	<link rel="stylesheet" href="style.css">
	<script>
		var thermostatData;

		function getPos(el) {
			for (var lx=0, ly=0;
			el != null;
			lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
			return {x: lx,y: ly};
		}

		function elementMouseOver(e){
			var mapPos = getPos(document.getElementById("map-svg"));
			var temp = this.getAttributeNS(null, "temperature");
			document.getElementById("mouseTooltip").innerHTML = temp + "&deg;";
			document.getElementById("mouseTooltip").style.opacity = 1;
			document.getElementById("mouseTooltip").style.left = e.clientX + mapPos.x + 15 + "px";
			document.getElementById("mouseTooltip").style.top = e.clientY + mapPos.y + 15 + "px";
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

		function mapLoaded() {
			resetStyles();
			getRooms();
			setInterval(getRooms,5*60*1000);
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

		function HSVtoRGB(h, s, v) {
			var r, g, b, i, f, p, q, t;
			i = Math.floor(h * 6);
			f = h * 6 - i;
			p = v * (1 - s);
			q = v * (1 - f * s);
			t = v * (1 - (1 - f) * s);
			switch (i % 6) {
				case 0: r = v, g = t, b = p; break;
				case 1: r = q, g = v, b = p; break;
				case 2: r = p, g = v, b = t; break;
				case 3: r = p, g = q, b = v; break;
				case 4: r = t, g = p, b = v; break;
				case 5: r = v, g = p, b = q; break;
			}
			return "#" + componentToHex( Math.floor(r * 255)) + componentToHex(Math.floor(g * 255)) + componentToHex(Math.floor(b * 255));
		}

		function rgbToHex(r, g, b) {
			return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
		}

		function tempToColor(temp)
		{
			var min = 68;
			var max = 80;
			var range = max - min;

			var Hue;
			if(temp < min){
				tmep = min;
			}else if(temp>max){
			temp = max;
			}

			Hue = ((range-(temp-min))*240/range)/360;
			var S = 1;
			var V = 1;

			return HSVtoRGB(Hue, 1, 1);
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
					thermostatData = JSON.parse(SerReturn);

					setupRoom("ResourceNarthex", "Worship Center Lobby");

					var WorshipCenterTemp = Math.round((Number(getTemp("Worship Center Stage")) + Number(getTemp("Worship Center West")) + Number(getTemp("Worship Center East")))/3);

					changeElementColor("ResourceE_Worship_Center", tempToColor(WorshipCenterTemp),WorshipCenterTemp);
					var svg = document.getElementById("map-svg");
					var svgDoc = svg.contentDocument;
					element = svgDoc.getElementById("ResourceE_Worship_Center");
					element.addEventListener("mousemove",elementMouseOver);
					element.addEventListener("mouseleave",mouseLeave);

					setupRoom("ResourceE_Choir_Room", "Choir Room");
					setupRoom("ResourceE_Chapel", "Chapel");
					setupRoom("ResourceE_Prayer_Green_Room", "Green Room");
					setupRoom("ResourceE_Fireside_Room_and_Kitchen","Fireside Room");
					setupRoom("ResourceErre","Mike's Office");
					setupRoom("ResourceE_Choir_Room", "Choir Room");
					setupRoom("ResourceNC_200_Amphitheater", "NC Amph");
					setupRoom("ResourceB_110", "B110");


				}
			}
			xmlhttp.open("GET", "scripts/getThermostats.php", true);
			xmlhttp.send();
		}
    </script>
<title>Campus Temperatures</title></head>
  <body>
<?php
	include 'menu.php';
?>
  <!-- Begin page content -->
	<div class="container">
			<div>
				<object id="map-svg" title="HellO" width="100%" type="image/svg+xml" data="Map.svg" onload="mapLoaded()"></object>
			</div>
			<br>
			<div id="test"></div>
			<div id="mouseTooltip" >Temperature</div>
	</div>
</body>
</html>