<?php
require "template.php"; 
$template = new template();
$rString = $template->get_random_string(10);
$movie = $_GET["movie"];
$length = shell_exec("/usr/local/bin/ffmpeg -i movies/\"$movie\" 2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
$length = explode(":",$length);
$length = $length[0]*3600 + $length[1]*60 + $length[2];
if($template->getBrowser() != "Firefox")
{
	header('Content-type: video/mp4');
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: atteachment; filename="'.$rString.'.mkv"');
	if(!empty($_GET['time']))
	{
		$time = $_GET['time'];
		$cmd = "/usr/local/bin/ffmpeg -ss $time -i movies/\"$movie\"  -c:v libx264 -acodec aac -strict -2 -b:a 192k -af \"volume=10dB\" -preset ultrafast -threads 0 -f matroska -";
	}
	else
	{
		$cmd = "/usr/local/bin/ffmpeg -i movies/\"$movie\" -c:v libx264 -acodec aac -strict -2 -b:a 192k -af \"volume=10dB\" -preset ultrafast -threads 0 -f matroska -";
	}
}
else
{
	header('Content-type: video/ogg');
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: atteachment; filename="'.$rString.'.ogg"');
	if(!empty($_GET['time']))
	{
		$time = $_GET['time'];
		$cmd = "/usr/local/bin/ffmpeg -ss $time -i movies/\"$movie\" -af \"volume=10dB\" -threads 2 -f ogg -";
	}
	else
	{
		$cmd = "/usr/local/bin/ffmpeg -i movies/\"$movie\" -af \"volume=10dB\" -threads 2 -f ogg -";
	}
}
passthru($cmd);
?>