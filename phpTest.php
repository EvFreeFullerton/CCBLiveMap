<?php
class CalEvent
{
	public $startTime;
	public $endTime;
	public $resources;
}

function CalculateDailyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$ret = array();
	
	if(strpos($recurrance,"Every day") === false || $endTime < $event->startTime || $absoluteEnd < $startTime)
		return "";
	
	$temp = $startTime;
	
	while ($temp <= $endTime){
		$newEvent = new CalEvent();
		$newEvent->resources = $event->resources;
		$newEvent->StartTime = date_create_from_format("Y-m-d H:i:s", $temp->format("Y-m-d")." ".$event->startTime->format("H:i:s"));
		$newEvent->EndTime = date_create_from_format("Y-m-d H:i:s", $temp->format("Y-m-d")." ".$event->endTime->format("H:i:s"));
		if($newEvent->StartTime > $absoluteEnd)
			return ret;
		if($newEvent->startTime->format("Y-m-d") != $event->startTime->format("Y-m-d"))
			array_push($ret, $temp);
		$temp->add(date_interval_create_from_date_string('1 day'));
	}
}

function CalculateWeeklyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$ret = array();
		
	if(strpos($recurrance,"Every week") === false || $endTime < $event->startTime || $absoluteEnd < $startTime){
		return $ret;
	}

	$searchDate1 = clone $startTime;
	$searchDate2;
	
	for($i = 0; $i < 7; $i++){
		if($searchDate1 > $endTime)
			break;
		if(strpos($recurrance,$searchDate1->format("l")) !== false)
		{
			$searchDate2 = clone $searchDate1;
			while($searchDate2 <= $endTime){
				if($searchDate2 > $endTime)
					break;
				$newEvent = new CalEvent();
				$newEvent->resources = $event->resources;
				$newEvent->startTime = date_create_from_format("Y-m-d H:i:s", $searchDate2->format("Y-m-d")." ".$event->startTime->format("H:i:s"));
				$newEvent->endTime = date_create_from_format("Y-m-d H:i:s", $searchDate2->format("Y-m-d")." ".$event->endTime->format("H:i:s"));
				//Checks: Not the original event, fits in the given range
				if($newEvent->startTime->format("Y-m-d") != $event->startTime->format("Y-m-d") && $newEvent->startTime < $endTime && $newEvent->endTime > $startTime)
					array_push($ret, $newEvent);
				$searchDate2->add(date_interval_create_from_date_string('7 days'));
			}
		}
		$searchDate1->add(date_interval_create_from_date_string('1 day'));
	}
	return $ret;
}

function findAllOccurances($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$eventList = array();
	if($event->startTime <= $endTime && $event->endTime >= $startTime){
		array_push($eventList, $event);
	}
//	array_push ($eventList,CalculateDailyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd));
	$eventList = array_merge ($eventList,CalculateWeeklyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd));
	return $eventList;
}

ini_set('max_execution_time', 5);

$user="ccbtest";
$password="ccbtest";
$database="ccballevents";
$connect = mysqli_connect("localhost",$user,$password);
if(!$connect){
	die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
}

mysqli_select_db($connect, $database) or die("Unable to select database.");

$now = date("Y-m-d H:i:s");
$query = "SELECT * FROM `eventlist` WHERE StartTime < '".$now."' && AbsoluteEnd > '".$now."'";

$startTime = date_create_from_format("Y-m-d H:i:s",date("Y-m-d H:i:s"));
$endTime = $startTime;

$resources = "[";

if ($result = $connect->query($query)) {

    /* fetch object array */
    while ($row = $result->fetch_row()) {
		$newEvent = new CalEvent();
		$newEvent->startTime = date_create_from_format("Y-m-d H:i:s",$row[3]);
		$newEvent->endTime = date_create_from_format("Y-m-d H:i:s",$row[4]);
		$newEvent->resources = $row[7];
		
		$array = findAllOccurances($startTime , $endTime,$newEvent, $row[6],date_create_from_format("Y-m-d H:i:s", $row[5]));
		foreach($array as $event) {
			$resourceRow = json_decode($row[7]);
			foreach($resourceRow as $r)
				$resources = $resources . '"' . str_replace("'","''",$r) . '",';
		}
    }
	
	$resources = str_replace(",]","]",$resources . "]");

	echo $resources;
	
    $result->close();
}

echo mysqli_error($connect);

mysqli_close($connect);
?>
