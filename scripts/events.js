    var events;
		var currentTime;
		var liveRefreshInterval;
		var lastEventDataRefresh;

		var originX = 0;
		var originY = 0;
		var originWidth = 10;
		var originHeight = 10;

		var zoomInterval;
		var currentX1;
		var currentY1;
		var currentWidth;
		var currentHeight;
		var destinationX1;
		var destinationY1;
		var destinationWidth;
		var destinationHeight;
		var progress;
		var svgMaster;
		var dp;

		function viewboxZoom()
		{
			newX = (destinationX1-currentX1)*progress + currentX1;
			newY = (destinationY1-currentY1)*progress + currentY1;
			newWidth = (destinationWidth-currentWidth)*progress +currentWidth;
			newHeight = (destinationHeight-currentHeight)*progress +currentHeight;
			svgMaster.setAttributeNS(null, "viewBox",newX+" "+newY+" "+newWidth+" "+newHeight);
			if(progress >= 1)
				clearInterval(zoomInterval);
			progress = progress + dp;
		}

		function modifyBox(x1,y1,width,height)
		{
			var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementById("svg2");

			currentPos = all.getAttributeNS(null,"viewBox").split(" ");

			currentX1 = Number(currentPos[0]);
			currentY1 = Number(currentPos[1]);
			currentWidth=Number(currentPos[2]);
			currentHeight=Number(currentPos[3]);
			destinationX1 = x1;
			destinationY1 = y1;
			destinationWidth = width;
			destinationHeight = height;
			progress = 0;
			duration = 2000;
			interval = 20;
			svgMaster = all;
			dp = interval/duration;
			zoomInterval = setInterval(viewboxZoom,interval);

		}

		function zoomToElement(elementName)
		{
			var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var element = svgDoc.getElementById(elementName);

			elementX = Number(element.getAttributeNS(null, "x"));
			elementY = Number(element.getAttributeNS(null, "y"));
			width = Number(element.getAttributeNS(null, "width"));
			height = Number(element.getAttributeNS(null,"height"));

			elementX = elementX - 114.37822;
			elementY = elementY - 328.77637;

			modifyBox(elementX,elementY,width,height);
		}

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
			document.getElementById("mouseTooltip").style.left = e.clientX + mapPos.x + 15 +"px";
			document.getElementById("mouseTooltip").style.top = e.clientY + mapPos.y + 15 + "px";
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

			var svg = document.getElementById("map-svg");
            var svgDoc = svg.contentDocument;
            var all = svgDoc.getElementById("svg2");
			currentPos = all.getAttributeNS(null,"viewBox").split(" ");
			originX = Number(currentPos[0]);
			originY = Number(currentPos[1]);
			originWidth=Number(currentPos[2]);
			originHeight=Number(currentPos[3]);

			resetStyles();
			sliderToCurrentTime();
			getRooms();
			refreshMap();
			LiveMode();
			window.setInterval(eventDataRefreshCallback,30000);
			window.setInterval(zoomToRandomRegion,10000);
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

		$( "#time" ).slider({range: false});

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

		function sliderX2Change(){

			modifyBox($("#x1").slider("value"),$("#y1").slider("value"),$("#x2").slider("value"),$("#y2").slider("value"));
		}

		function zoomToRandomRegion(){
			var svg = document.getElementById("map-svg");
			var svgDoc = svg.contentDocument;
			var all = svgDoc.getElementsByTagName("*");
			var ActiveIds = [];

			if(!document.getElementById("zoomCheckBox").checked)
				return;

			if(events == null)
				return;

			for (var i = 0; i < all.length; i++) {

				if (all[i].id == null || all[i].id.substring(0, 6) != "Region"){
					continue;
				}
				ActiveIds.push(all[i].id);
			}

			var gotoIndex = Math.floor((ActiveIds.length+1)*Math.random());
			if(gotoIndex == 0)
				modifyBox(originX,originY,originWidth,originHeight);
			else{
				zoomToElement(ActiveIds[gotoIndex-1]);
			}
		}

		function zoomBoxChanged()
		{
			var checkbox = document.getElementById("zoomCheckBox");
			if(checkbox.checked)
				zoomToRandomRegion();
			else
				modifyBox(originX,originY,originWidth,originHeight);
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