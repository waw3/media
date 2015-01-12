<?php
require "vendor/autoload.php";
$core = new core();
$rString = $core->get_random_string(10);
header('Content-type: video/mp4');
header('Content-Disposition: atteachment; filename="'.$rString.'.mkv"');

if(!empty($_GET['time'])){$time = $_GET['time'];}
else { $time = 0; }
if(!empty($_GET['show']))
{
	$media = escapeshellarg($_GET['show']);
}
else if(!empty($_GET['movie']))
{
	$media = escapeshellarg("movies/".$_GET["movie"]);
}
else{exit();}
$cmd = "/usr/local/bin/ffmpeg -ss $time -i $media -c:v libx264 ".
"-vf \"format=yuv420p\" -preset veryfast -crf 18 -acodec aac -strict".
" -2 -b:a 320k -threads  2  -f matroska -";
passthru($cmd);
?>