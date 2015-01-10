<?php
require "template.php"; 
$template = new template();
$rString = $template->get_random_string(10);
$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
if(!empty($_GET['show']))
{
	if(!empty($_GET['time'])){$time = $_GET['time'];}
	else { $time = 0; }
	$show = $_GET['show'];
	$show = escapeshellarg($show);
	header('Content-type: video/mp4');
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: atteachment; filename="'.$rString.'.mkv"');
	$cmd = "/usr/local/bin/ffmpeg -ss $time -i $show -c:v libx264 -vf \"format=yuv420p\" -preset veryfast -crf 18 -acodec aac -strict -2 -b:a 320k -threads  2  -f matroska -";
	passthru($cmd);
}
else if(stripos(implode($files, " "),substr($_GET["movie"],0,
strlen($_GET["movie"])-4)) !== false )
{
	$movie = $_GET["movie"];
	if($template->getBrowser() != "Firefox")
	{
		header('Content-type: video/mp4');
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: atteachment; filename="'.$rString.'.mkv"');
		if(!empty($_GET['time'])){$time = $_GET['time'];}
		else { $time = 0; }
		$cmd = "/usr/local/bin/ffmpeg -ss $time ";
		if($_GET['quality'] == "source")
		{ 
			$cmd = "/usr/local/bin/ffmpeg -ss $time -itsoffset 10 ";
			$vcodec = "copy  ";
		}
		else if($_GET['quality'] == "High")
		{
			$codec = "libx264 -vf \"format=yuv420p\" -preset veryfast -crf 18 -acodec aac -strict -2 -b:a 320k -threads  0 ";
		}
		else if($_GET['quality'] == "Medium")
		{
			$codec = "libx264 -vf \"format=yuv420p\" -preset veryfast -crf 25 -acodec aac -strict -2 -b:a 192k -threads  0 ";
		}
		else if($_GET['quality'] == "Low")
		{
			$codec = "libx264 -vf \"format=yuv420p\" -preset veryfast -crf 31 -acodec aac -strict -2 -b:a 128k -threads  0 ";
		}
		
		$cmd .= " -i movies/\"$movie\" -c:v $codec -f matroska -";
	}
	else
	{
		header('Content-type: video/ogg');
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: atteachment; filename="'.$rString.'.ogg"');
		if(!empty($_GET['time']))
		{
			$time = $_GET['time'];
			$cmd = "/usr/local/bin/ffmpeg -ss $time -i movies/\"$movie\" -threads 2 -f ogg -";
		}
		else
		{
			$cmd = "/usr/local/bin/ffmpeg -i movies/\"$movie\" -af \"volume=10dB\" -threads 2 -f ogg -";
		}
	}

		passthru($cmd);
}
?>
