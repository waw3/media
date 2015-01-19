<?php
require "vendor/autoload.php";
$Core = new Core();
//getting global bitrate setting.
$globalBR = Media::configInfo("bitrate");

$rString = Media::get_random_string(10);
$media = $_GET['media'];

//checking time to start transcoding.
if(!empty($_GET['time'])){$time = escapeshellarg($_GET['time']);}
else { $time = 0; }
//getting media bitrate info.
$movieRate = Media::movieInfo($media,"vBitrate");

//checking if it should be transcoded.

if($Core->getBrowser() == "Firefox"){$transcode = true;}
else if($globalBR < $movieRate){$transcode = true;}
else if(isset($_GET['br'])){$transcode = true;}
else if(Media::movieInfo($media,"vCodec") != "h264"){$transcode = true;}
else if(Media::movieInfo($media,"aCodec") != "aac"){$transcode = true;}
header('Content-Length: '.Media::movieInfo($media,"size"));
$media = escapeshellarg($media);

if($transcode)
{
	//If it has to be transcoded then we are going to set some bitrate info.
	if(isset($_GET['br']) && is_numeric($_GET['br']) && $movieRate > $_GET['br'] || $globalBR < $movieRate)
	{
		if(!isset($_GET['br'])){$br = $globalBR;}
		else if($globalBR < $_GET['br']){$br = $globalBR;}
		else{$br = $_GET['br'];}
		if($br <= 1024){$scale = "-vf scale=640:-1";}
		else if($br <= 1536){$scale = "-vf scale=960:-1";}
		else if($br <= 2048){$scale = "-vf scale=1280:-1";}
		else if($br <= 4096){$scale = "-vf scale=1920:-1";}
		if($br <= 3072 && $br >= 1536){$aBr = 320;}
		else if($br >= 256) { $aBr = 192;}
		$bitrate = '-b:v '.$br.'k -b:a '.$aBr.'k -minrate '.
		$br.'k -bufsize '.$br*2 .'k ';
	}
	else{$bitrate = '-b:v '.$movieRate.'k -bufsize '.$movieRate*2 .'k ';}
	$command = "/sbin/sysctl -a | egrep -i 'hw.ncpu'| awk '{print $2}'";
	$threads = shell_exec($command)-1;
	header('Content-type: video/webm');
	header('Content-Disposition: attachment; filename="'.$rString.'.webm"');
	//Using ffmpeg from the settings above. This is using the webm container.
	if($Core->getBrowser() == "Firefox" || $Core->getBrowser() == "MSIE" ){$cmd = "/usr/local/bin/ffmpeg -ss $time -re ";}
	else{$cmd = "/usr/local/bin/ffmpeg -ss $time -re ";}
	$cmd .= " -i $media -c:v libvpx -vf \"format=yuv420p\"".
	" -cpu-used 5 -acodec libvorbis $bitrate $scale -threads $threads  -f webm -";
}
else
{
	header('Content-type: video/webm');
	header('Content-Disposition: attachment; filename="'.$rString.'.webm"');

	$cmd = "/usr/local/bin/ffmpeg -ss $time -i $media -c:v copy -c:a copy  -f matroska -";
}
ob_end_clean();
passthru($cmd);
?>