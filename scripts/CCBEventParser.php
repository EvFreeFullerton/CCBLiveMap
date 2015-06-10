<?php
require '../config/MySQLCredentials.php';

class CalEvent implements JsonSerializable
{
	public $id;
	public $name;
	public $startTime;
	public $endTime;
	public $resources;
	public $group;
	public $description;
	public $exceptions;
	
	public function jsonSerialize()
	{
		return [
				'id' => $this->id,
				'name' => $this->name,
				'startTime' => $this->startTime->format("U"),
				'endTime' => $this->endTime->format("U"),
				'resources' => $this->resources,
				'group' => $this->group,
				'description' => $this->description,
				'exceptions' => $this->exceptions
			];
	}
}

function CalEventSort($a,$b)
{
	return $a->startTime > $b->startTime;
}

function calculateMonthlyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$ret = array();
		
	if(strpos($recurrance,"Every month") === false || $endTime < $event->startTime || $absoluteEnd < $startTime){
		return $ret;
	}
	
	$pattern = '/the (?<occurance>\\w+) (?<day>\\w+) of the month/';
	preg_match_all($pattern, $recurrance, $matches);
	
	$searchMonth = date_create_from_format("Y-m-d H:i:s",$startTime->format("Y-m")."-01 00:00:00");
	
	if($searchMonth < date_create_from_format("Y-m-d H:i:s",$event->startTime->format("Y-m")."-01 00:00:00"))
	{
		$searchMonth = date_create_from_format("Y-m-d H:i:s",$event->startTime->format("Y-m")."-01 00:00:00");
	}
	
	while($searchMonth < $endTime)
	{
		for($i=0;$i<count($matches['occurance']);$i++)
		{
			$occurance = 0;
			
			switch($matches['occurance'][$i])
			{
				case "first": $occurance = 1; break;
				case "second": $occurance = 2; break;
				case "third": $occurance = 3; break;
				case "fourth": $occurance = 4; break;
				case "fifth": $occurance = 5; break;
			}
			
			for($x=1;$x<=cal_days_in_month(CAL_GREGORIAN, intval($searchMonth->format("m")), intval($searchMonth->format("Y")));$x++){
				if(strtolower(date_create_from_format("Y-m-d H:i:s",$searchMonth->format("Y-m")."-".$x." 00:00:00")->format("l")) == strtolower($matches['day'][$i])){
					$occurance--;
					if($occurance == 0){
						$occurance=$x;
						break;
					}else{
						$x += 6;
					}
				}
			}
			
			$newStart = date_create_from_format("Y-m-d H:i:s",$searchMonth->format("Y-m-").$occurance." ".$event->startTime->format("H:i:s"));
			$newEnd = date_create_from_format("Y-m-d H:i:s",$searchMonth->format("Y-m-").$occurance." ".$event->endTime->format("H:i:s"));
			if($newStart <= $absoluteEnd && $newStart > $event->startTime && $newStart < $endTime && $newEnd > $startTime && $newStart->format("Y-m-d") != $event->startTime->format("Y-m-d") && !isException($newStart,$event))
			{
				$newEvent = clone $event;
				$newEvent->startTime = $newStart;
				$newEvent->endTime = $newEnd;
				array_push($ret,$newEvent);
			}
		}

		$searchMonth->add(date_interval_create_from_date_string('1 month'));
		if($searchMonth > $absoluteEnd)
			break;
	}
	
	return $ret;
}

function CalculateDailyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$ret = array();
	
	if(strpos($recurrance,"Every day") === false || $endTime < $event->startTime || $absoluteEnd < $startTime)
		return $ret;
	
	$temp = clone $startTime;
	
	if($temp < $event->startTime)
		$temp = clone $event->startTime;
	
	//Don't create a new occurrence on the start date of the event
	if($temp->format("Y-m-d") == $event->startTime->format("Y-m-d"))
		$temp->add(date_interval_create_from_date_string('1 day'));
	
	while ($temp <= $endTime){
		$newEvent = clone $event;
		$newEvent->startTime = date_create_from_format("Y-m-d H:i:s", $temp->format("Y-m-d")." ".$event->startTime->format("H:i:s"));
		$newEvent->endTime = date_create_from_format("Y-m-d H:i:s", $temp->format("Y-m-d")." ".$event->endTime->format("H:i:s"));
		if($newEvent->startTime > $absoluteEnd && !isException($newEvent->startTime,$event))
			return $ret;
		
		array_push($ret, $newEvent);

		$temp->add(date_interval_create_from_date_string('1 day'));
	}
	return $ret;
}

//Need to optimize checks for event time range within given time range
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
				$newEvent = clone $event;
				$newEvent->startTime = date_create_from_format("Y-m-d H:i:s", $searchDate2->format("Y-m-d")." ".$event->startTime->format("H:i:s"));
				$newEvent->endTime = date_create_from_format("Y-m-d H:i:s", $searchDate2->format("Y-m-d")." ".$event->endTime->format("H:i:s"));
				//Checks: don't duplicate the original event, verify start & end time range
				if($newEvent->startTime->format("Y-m-d") != $event->startTime->format("Y-m-d") && $newEvent->startTime < $endTime && $newEvent->endTime > $startTime && !isException($newEvent->startTime, $event) && $newEvent->startTime > $event->startTime && $newEvent->endTime <= $absoluteEnd)
					array_push($ret, $newEvent);
				$searchDate2->add(date_interval_create_from_date_string('7 days'));
			}
		}
		$searchDate1->add(date_interval_create_from_date_string('1 day'));
	}
	return $ret;
}

function isException($startTime, $event){
	return in_array($startTime->format("Y-m-d"),json_decode($event->exceptions));
}

function findAllOccurances($startTime, $endTime, $event, $recurrance, $absoluteEnd)
{
	$eventList = array();
	if($event->startTime <= $endTime && $event->endTime >= $startTime && !isException($event->startTime, $event)){
		array_push($eventList, $event);
	}
	$eventList = array_merge ($eventList, CalculateDailyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd));
	$eventList = array_merge ($eventList,CalculateWeeklyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd));
	$eventList = array_merge ($eventList,CalculateMonthlyRecurrancesInRange($startTime, $endTime, $event, $recurrance, $absoluteEnd));
	$exceptionArray = json_decode($event->exceptions);
	return $eventList;
}

function getAllEventsInRange($startTime, $endTime, $sorted)
{
	$database="ccballevents";
	$connect = mysqli_connect("localhost",MySQLCredentials()['user'],MySQLCredentials()['password']);
	if(!$connect){
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	}

	mysqli_select_db($connect, $database) or die("Unable to select database.");

	$query = "SELECT * FROM `eventlist` WHERE StartTime < '".$endTime->format("Y-m-d H:i:s")."' && AbsoluteEnd > '".$startTime->format("Y-m-d H:i:s")."'";

	$allEvents = array();

	if ($result = $connect->query($query)) {
		while ($row = $result->fetch_row()) {
			$newEvent = new CalEvent();
			$newEvent->id = $row[0];
			$newEvent->startTime = date_create_from_format("Y-m-d H:i:s",$row[3]);
			$newEvent->endTime = date_create_from_format("Y-m-d H:i:s",$row[4]);
			$newEvent->resources = $row[7];
			$newEvent->name = $row[1];
			$newEvent->group = $row[8];
			$newEvent->exceptions = $row[9];
			$newEvent->description = $row[2];
			
			$array = findAllOccurances($startTime , $endTime,$newEvent, $row[6],date_create_from_format("Y-m-d H:i:s", $row[5]));

			$allEvents = array_merge ($allEvents, $array);
		}

		if($sorted)
			usort($allEvents,"CalEventSort");

		$result->close();
		
		return $allEvents;
	}

	echo mysqli_error($connect);

	mysqli_close($connect);
}
?>
