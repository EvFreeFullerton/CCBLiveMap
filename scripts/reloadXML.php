<?php
require 'CCBCredentials.php';

error_reporting(E_ALL);
ini_set('display_errors', 'on');
require 'MySqlcredentials.php';
ini_set('max_execution_time', 1000);

function getSslPage($url) {
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD,CCBCredentials()['user'].":".CCBCredentials()['password']);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$user=MySQLCredentials()['user'];
$password=MySQLCredentials()['password'];
$database="ccballevents";
$connect = mysqli_connect("localhost",$user,$password);
if(!$connect){
	die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
}

mysqli_select_db($connect, $database) or die("Unable to select database.");

$XML = getSslPage("https://evfreefullerton.ccbchurch.com/api.php?srv=event_profiles");
$CCBEventsDB = simplexml_load_string($XML);

mysqli_query($connect, "UPDATE `eventlist` SET `dirty`=1");
echo mysqli_error($connect);

foreach($CCBEventsDB->response->events->event as $event)
{
	$resources = "[";
	foreach($event->resources->resource as $resource){
			$resources = $resources . '"' . str_replace("'","''",$resource->name) . '",';
	}
	$resources = str_replace(",]","]",$resources . "]");
	
	$exceptions = "[";
	foreach($event->exceptions->exception as $exception){
			$exceptions = $exceptions . '"' . str_replace("'","''",$exception->date) . '",';
	}
	$exceptions = str_replace(",]","]",$exceptions . "]");
	
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
					$resources .  "','" .
					str_replace("'","''",$event->group).  "','" .
					$exceptions . "','". '0' .
					"')";
	$query = "REPLACE INTO eventlist VALUES ". $values;
	mysqli_query($connect,$query);
	
	$query = 'DELETE FROM `eventlist` WHERE `dirty`=1';
	mysqli_query($connect,$query);
	
	if(mysqli_errno($connect) != 0)
		echo mysqli_error($connect) . "<br>";
}

mysqli_close($connect);
?>
