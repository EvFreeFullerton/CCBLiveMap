<?php

ini_set('max_execution_time', 600);

$user="ccbtest";
$password="ccbtest";
$database="ccballevents";
$connect = mysqli_connect("localhost",$user,$password);
if(!$connect){
	die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
}

mysqli_select_db($connect, $database) or die("Unable to select database.");

$CCBEventsDB = simplexml_load_file("ccbEventDB.xml");

mysqli_query($connect,"DELETE FROM eventlist");
echo mysqli_error($connect) . "<br>";

foreach($CCBEventsDB->response->events->event as $event)
{
	$resources = "[";
	foreach($event->resources->resource as $resource){
			$resources = $resources . '"' . str_replace("'","''",$resource->name) . '",';
	}
	$resources = str_replace(",]","]",$resources . "]");
	
	if(strpos($event->recurrence_description,"until") === false){
		if(strpos($event->recurrence_description,"Every month") === false && strpos($event->recurrence_description,"Every week") === false && strpos($event->recurrence_description,"Every day") === false){
			$until = $event->end_datetime;
		}else{
			$until = "2100-01-01 00:00:00";
		}
	}else{
		preg_match("/until (\w{3}) (\d+), (\d{4})/", $event->recurrence_description, $output_array);
		switch($output_array[1]){
			case "Jan": $month = "01"; break;
			case "Feb": $month = "02"; break;
			case "Mar": $month = "03"; break;
			case "Apr": $month = "04"; break;
			case "May": $month = "05"; break;
			case "Jun": $month = "06"; break;
			case "Jul": $month = "07"; break;
			case "Aug": $month = "08"; break;
			case "Sep": $month = "09"; break;
			case "Oct": $month = "10"; break;
			case "Nov": $month = "11"; break;
			case "Dec": $month = "12"; break;
		}
		$until = $output_array[3]."-".$month."-".$output_array[2]." 23:59:59";
	}
	
	//id, name, description, starttime, endtime, absoluteend, recurrence
	$values = "('" . $event['id'] . "','" .
					str_replace("'","''",$event->name) . "','" .
					str_replace("'","''",$event->description) . "','" .
					$event->start_datetime . "','" .
					$event->end_datetime . "','" .
					$until . "','" .
					$event->recurrence_description . "','" .
					$resources . "')";
	$query = "INSERT INTO eventlist VALUES ". $values;
	mysqli_query($connect,$query);
	if(mysqli_errno($connect) != 0)
		echo mysqli_error($connect) . "<br>";
}

mysqli_close($connect);
?>