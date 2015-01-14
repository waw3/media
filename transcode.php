<?php
require "vendor/autoload.php";
$core = new core();
$rString = $core->get_random_string(10);
header('Content-type: video/mp4');
header('Content-Disposition: attachment; filename="'.$rString.'.mkv"');
$dir = escapeshellarg($_GET['media']);
$vTranscode = false;
$aTranscode = false;
if(!empty($_GET['time'])){$time = escapeshellarg($_GET['time']);}
else { $time = 0; }
if(strpos($dir,"shows") === false){$media = "movies/".$dir;}
else{$media = $dir;}
$movieRate = shell_exec("/usr/local/bin/ffprobe -i $media 2>&1 | grep bitrate | awk '{print $6}'");
if(!empty($_GET['br']) && is_numeric($_GET['br']) && $movieRate > $_GET['br'])
{
	$br = $_GET['br'];
	$vBr = $br;
	if($br <= 3072 && $br >= 1536){$aBr = 320;}
	else if($br >= 256) { $aBr = 192;}
	$bitrate = '-b:v '.$vBr.'k -b:a '.$aBr.'k -minrate '.$vBr.'k -bufsize '.$vBr*2 .'k ';
}
else{$bitrate = "";}
$cmd = "/usr/local/bin/ffmpeg -ss $time -itsoffset 10 -i $media -c:v libx264 -vf \"format=yuv420p\" -acodec aac -strict -2 -cutoff 15000 -ac 2 $bitrate".
" -threads 0  -f matroska -";
passthru($cmd);
?>