<?php
require "vendor/autoload.php";
$core = new core();
$core->startSessionRestricted();
if(empty($_SESSION['username'])){exit();}
if($_GET['time'] <= 0){exit();}
if(!(isset($_GET['media']) && 
isset($_GET['time']) && is_numeric($_GET['time']))){exit();}
if(!file_exists($_GET['media'])){exit();}

$media = $_GET['media'];
$cw = @file_get_contents("Logs/".
$_SESSION['username']."_cw.json");
if($cw !== false)
{
	$cw = json_decode($cw,true);

	
	if(count($cw) > 15)
	{
		array_shift($cw);
	}
}
if(strpos($media,"movies") !== false){$value = 780;}
else{$value = 60;}

if(($_GET['time']+$value) > $core->movieInfo($media,"length"))
{
	if(isset($cw["$media"]))
	{	
		unset($cw["$media"]);
		$cw = array_filter($cw);
		file_put_contents("Logs/".$_SESSION['username'].
		"_cw.json",json_encode($cw));
	}
}
else
{
	$cw["$media"] = array( "0" => $_GET['time'], 
	"1" => $core->movieInfo($media,"length"));
	file_put_contents("Logs/".$_SESSION['username'].
	"_cw.json",json_encode($cw));
}


?>