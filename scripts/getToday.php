<?php
require 'CCBEventParser.php';

$startTime = date_create_from_format("Y-m-d H:i:s",date("Y-m-d")."00:00:00");
$endTime = date_create_from_format("Y-m-d H:i:s",date("Y-m-d")."23:59:59");

$allEvents = getAllEventsInRange($startTime, $endTime, false);

echo json_encode($allEvents);

?>
