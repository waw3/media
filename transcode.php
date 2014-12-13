<?php
require "template.php"; 
$template = new template();
$rString = $template->get_random_string(10);
$movie = $_GET["movie"];
header('Content-type: video/mp4');
header("Content-Type: application/octet-stream");
header('Content-Disposition: atteachment; filename="'.$rString.'.mkv"');
$length = shell_exec("/usr/local/bin/ffmpeg -i movies/\"$movie\" 2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
$length = explode(":",$length);
$length = $length[0]*3600 + $length[1]*60 + $length[2];
header('Content-Duration: 33');

if(!empty($_GET['time']))
{
	$time = $_GET['time'];
	$cmd = "/usr/local/bin/ffmpeg -i movies/\"$movie\" -ss $time -c:v libx264 -acodec aac -strict -2 -b:a 192k  -preset ultrafast -f matroska -";
}
else
{
	$cmd = "/usr/local/bin/ffmpeg -i movies/\"$movie\" -c:v libx264 -acodec aac -strict -2 -b:a 192k  -preset ultrafast -f matroska -";
}
passthru($cmd);
?>