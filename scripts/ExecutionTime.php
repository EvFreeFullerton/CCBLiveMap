<?php
$start = microtime(true);
require 'CCBEventParser.php';

$startTime = date_create_from_format("Y-m-d H:i:s","2015-5-01 00:00:00");
$endTime = date_create_from_format("Y-m-d H:i:s","2015-5-30 23:59:59");

$allEvents = getAllEventsInRange($startTime, $endTime, false);

$time_elapsed_secs = round((microtime(true) - $start)*1000);
echo "<h4>".$time_elapsed_secs." Milliseconds</h4>";

echo json_encode($allEvents);

//Initial:
//1 day: 60 ms
//30 days: 120 ms
//1 year: 1.5 seconds

?>
