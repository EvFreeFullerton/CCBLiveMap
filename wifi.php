<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <style type="text/css">

      /* Custom page CSS
      -------------------------------------------------- */
      /* Not required for template or sticky footer method. */

      .container {
        width: auto;
        max-width: 680px;
      }
    </style>

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
			var temp = this.getAttributeNS(null, "tooltip");
			document.getElementById("mouseTooltip").innerHTML = temp;
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

		function changeElementText(name, textV){
		     var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementById(name);
			all.textContent= textV;
		}

		function mapLoaded() {
			getRooms();
			setInterval(getRooms,5*60*1000);
		}

		function setupTooltip(AP, text)
		{
			var svg = document.getElementById("map-svg");
			var svgDoc = svg.contentDocument;
			element = svgDoc.getElementById("AP"+AP+"txt");
			element.addEventListener("mousemove",elementMouseOver);
			element.addEventListener("mouseleave",mouseLeave);
			element.setAttributeNS(null, "tooltip",  text);

			element = svgDoc.getElementById("AP"+AP+"Color");
			element.addEventListener("mousemove",elementMouseOver);
			element.addEventListener("mouseleave",mouseLeave);
			element.setAttributeNS(null, "tooltip",  text);
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
					var total=0;

					for(var i = 0;i<wifiData.length;i++){
						changeElementText("AP"+wifiData[i].mapID+"txt",wifiData[i].channelData[0]);
						var tooltip = "<H5>"+wifiData[i].name+"</H5>"+"<b>"+wifiData[i].channelNames[0] +": " + wifiData[i].channelData[0]+"</b><br>";
						total += Number(wifiData[i].channelData[0]);
						for(var x = 1;x<wifiData[i].channelData.length;x++)
							tooltip = tooltip + wifiData[i].channelNames[x] +": " + wifiData[i].channelData[x]+"<br>";
						setupTooltip(wifiData[i].mapID,tooltip);
					}

					changeElementText("wifiTotal",total);
				}
			}
			xmlhttp.open("GET", "scripts/getWifi.php", true);
			xmlhttp.send();
		}
    </script>
<title>Campus WiFi Usage</title></head>
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
</body>
</html>