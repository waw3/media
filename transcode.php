<?php
require "vendor/autoload.php";
$core = new core();
$globalBR = $core->configInfo("bitrate");
$rString = $core->get_random_string(10);
$dir = $_GET['media'];
$vTranscode = false;
$aTranscode = false;
if(!empty($_GET['time'])){$time = escapeshellarg($_GET['time']);}
else { $time = 0; }
if(strpos($dir,"shows") === false){$media = "movies/".$dir;}
else{$media = $dir;}
$movieRate = $core->movieInfo($media,"vBitrate");
$media = escapeshellarg($media);

//checking if the Get variable for bitrate is set
//also checking if it's a number and that the movie rate
// is less than the bitrate and that that bitrate is less than the server's
// global bitrate
if(isset($_GET['br']) && is_numeric($_GET['br']) && $movieRate > $_GET['br'] && $_GET['br'] < $globalBR)
{
	$br = $_GET['br'];
	if($br <= 1024){$scale = "-vf scale=640:-1";}
	else if($br <= 1536){$scale = "-vf scale=960:-1";}
	else if($br <= 2048){$scale = "-vf scale=1280:-1";}
	else if($br <= 4096){$scale = "-vf scale=1920:-1";}
	if($br <= 3072 && $br >= 1536){$aBr = 320;}
	else if($br >= 256) { $aBr = 192;}
	$bitrate = '-b:v '.$br.'k -b:a '.$aBr.'k -minrate '.$br.'k -bufsize '.$br*2 .'k ';
}
//checking if gobalBR is set and that it's less than movieRate
//if it is then we will adjust the bitrate to the globalBR
else if(!empty($globalBR) && $globalBR < $movieRate)
{
	$vBr = $globalBR;
	if($globalBR <= 1024){$scale = "-vf scale=640:-1";}
	else if($globalBR <= 1536){$scale = "-vf scale=960:-1";}
	else if($globalBR <= 2048){$scale = "-vf scale=1280:-1";}
	else if($globalBR <= 4096){$scale = "-vf scale=1920:-1";}
	if($globalBR <= 3072 && $globalBR >= 1536){$aBr = 320;}
	else if($globalBR >= 256) { $aBr = 192;}
	$bitrate = '-b:v '.$vBr.'k -b:a '.$aBr.'k -minrate '.$vBr.'k -bufsize '.$vBr*2 .'k ';
}
else{$bitrate = '-b:v '.$movieRate.'k -bufsize '.$movieRate*2 .'k ';}
if($core->getBrowser() == "Firefox" || $core->getBrowser() == "Chrome")
{	
	$command = "/sbin/sysctl -a | egrep -i 'hw.ncpu'| awk '{print $2}'";
	$threads = shell_exec($command)-1;
	header('Content-type: video/webm');
	header('Content-Disposition: attachment; filename="'.$rString.'.webm"');
	$cmd = "/usr/local/bin/ffmpeg -ss $time -i $media -c:v libvpx -vf \"format=yuv420p\" -cpu-used 16 -acodec libvorbis $bitrate $scale".
	" -threads $threads  -f webm -";
}
else
{
	header('Content-type: video/mp4');
	header('Content-Disposition: attachment; filename="'.$rString.'.mkv"');
	$cmd = "/usr/local/bin/ffmpeg -ss $time -i $media -c:v libx264 -vf \"format=yuv420p\" -acodec aac -strict -2 -cutoff 15000 -ac 2 $bitrate $scale".
	" -threads 0  -f matroska -";
}
passthru($cmd);
?>