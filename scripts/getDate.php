<?php
require 'CCBEventParser.php';

$day = $_GET["Year"]."-".$_GET["Month"]."-".$_GET["Day"];

$startTime = date_create_from_format("Y-m-d H:i:s",$day."00:00:00");
$endTime = date_create_from_format("Y-m-d H:i:s",$day."23:59:59");

$allEvents = getAllEventsInRange($startTime, $endTime, true);

echo json_encode($allEvents);

?>
