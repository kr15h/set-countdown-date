<?php

date_default_timezone_set("Europe/London");

// Create array with datetimes.
// These times are UTC +3.

/* 
18.10.2017 11.00
25.10.2017 11.00
01.11.2017 11.00
08.11.2017 11.00
15.11.2017 11.00
22.11.2017 11.00
*/

$arr = array();

// Using the JavaScript version of month here: 
// it starts from 0 (January); in PHP it starts from 1.
// And we are using UTC date here. 
array_push($arr, json_decode('{"year": 2017, "month": 9,  "day": 18, "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));
array_push($arr, json_decode('{"year": 2017, "month": 9,  "day": 25, "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));
array_push($arr, json_decode('{"year": 2017, "month": 10, "day": 1,  "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));
array_push($arr, json_decode('{"year": 2017, "month": 10, "day": 8,  "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));
array_push($arr, json_decode('{"year": 2017, "month": 10, "day": 15, "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));
array_push($arr, json_decode('{"year": 2017, "month": 10, "day": 22, "hour": 8, "minute": 0, "second": 0, "millisecond":0}'));

// 1. Get rid of the ones before current time
$tsNow = mktime();
for($i = 0; $i < count($arr); $i++){
	$tsEnt = mktime($arr[$i]->hour, $arr[$i]->minute, $arr[$i]->second, $arr[$i]->month + 1, $arr[$i]->day, $arr[$i]->year);
	if($tsEnt < $tsNow){
		array_splice($arr, $i, 1);
		$i--;
	}
}

// 2. Get the smallest remaining entry
$tsFut = mktime() * mktime(); // Some abstract time in the future
$jsonOut = 0;
for($i = 0; $i < count($arr); $i++){
	$tsEnt = mktime($arr[$i]->hour, $arr[$i]->minute, $arr[$i]->second, $arr[$i]->month + 1, $arr[$i]->day, $arr[$i]->year);
	if($tsEnt < $tsFut){
		$tsFut = $tsEnt;
		$jsonOut = $arr[$i];
	}
}

// 3. Read the current json and compare, if differ, overwrite
$file = fopen("end-date.json", "r");
$contents = fread($file, filesize("end-date.json"));
fclose($file);
$jsonCur = json_decode($contents);

$tsCur = mktime($jsonCur->hour, $jsonCur->minute, $jsonCur->second, $jsonCur->month + 1, $jsonCur->day, $jsonCur->year);
$tsOut = mktime($jsonOut->hour, $jsonOut->minute, $jsonOut->second, $jsonOut->month + 1, $jsonOut->day, $jsonOut->year);

if($tsCur != $tsOut){
	$fp = fopen('end-date.json', 'w');
	fwrite($fp, json_encode($jsonOut));
	fclose($fp);

	// Exit with a messae
	echo "Current end date reached, updated end-date.json with next date: $jsonOut->year-".($jsonOut->month + 1)."-$jsonOut->day\n";
}else{
	// Exit with an error code 0
	die(0);
}

?>