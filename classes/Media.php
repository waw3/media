<?php
date_default_timezone_set('America/Detroit');
class Media
{
	public static function compareDate($dateToCompare) 
	{
		date_default_timezone_set('America/Detroit');
		$currentDate=(strtotime(date('Y-m-d H:i:s')));
		$otherDate=(strtotime($dateToCompare));
		$difference=($currentDate - $otherDate)/3600;
		return round($difference,2);
	}
	// converts seconds into hours minutes seconds.
	public static function secondsToTime($seconds) 
	{
		$H=floor($seconds / 3600);
		$i=($seconds / 60) % 60;
		$s=$seconds % 60;
		return sechof("%02d:%02d:%02d", $H, $i, $s);
	}
	public static function videojsScripts()
	{
	?>
		<link href="//vjs.zencdn.net/4.5/video-js.css" rel="stylesheet">
		<script src="//vjs.zencdn.net/4.5/video.js"></script>
		<style type="text/css">
		.vjs-default-skin .vjs-play-progress,
		.vjs-default-skin .vjs-volume-level { background-color: #ff0000 }
		.vjs-default-skin .vjs-big-play-button { background: rgba(0,0,0,1) }
		.vjs-default-skin .vjs-slider background: rgba(0,0,0,0.3333333333333333) }
		</style>
	<?php
	}
	public static function videojs($videoname, $width, $height, $length = "",$time="")
	{
		$video = explode("?media=",$videoname);
		$video = urldecode($video[1]);
		if(strpos($video,"&time="))
		{
			$video = explode("&time=",$video);
			$video = $video[0];
		}
		if(strpos($video,"&br="))
		{
			$video = explode("&br=",$video);
			$video = $video[0];
		}
	
		?>
		<video id="MY_VIDEO_1"
		class="video-js vjs-default-skin vjs-big-play-centered" controls
		width="<?php print $width; ?>" 
		height="<?php print $height; ?>">
		<?php
		if(!empty($length))
		{
		
		?>
		<script type="text/javascript">
			var video= videojs('MY_VIDEO_1');
			var cbitrate = false;
			video.src("<?php print $videoname; ?>");
			video.load();
			setTimeout(function(){
			video.play();
			}, 2000);
			// hack duration
			video.duration= function() { return video.theDuration; };
			<?php if(!empty($time))
			{
				
			?>
			video.start = <?php print $time; ?>;
			<?php
			$time = "";
			}
			else
			{
			?>
			video.start= 0;
			<?php
			}
			?>
			video.oldCurrentTime= video.currentTime;
			video.currentTime= function(time) 
			{ 
				if( time == undefined )
				{
					return video.oldCurrentTime() + video.start;
				}
				video.oldCurrentTime(0);
				video.start = time;
				$.get( "time.php", { media: "<?php print $video;?>", 
				time: Math.trunc(time) } );
				playvideo("<?php print $videoname."&time="; ?>" + Math.trunc(time));
				video.load()
				//since the transcoding is real time a little buffer is nice.
				setTimeout(function(){
				video.play();
				}, 2000);
				return this;
			};
			video.theDuration=<?php print $length; ?>;
			setInterval(function() {
			$.get( "time.php", { media: "<?php print $video;?>",
			time: Math.trunc(video.currentTime()) } );
			}, 15000);
		</script>
		</video>
		<?php
		}
		else
		{
		?>
		<script>
			var video= videojs('MY_VIDEO_1');
			video.src("<?php print $videoname; ?>");
			video.currentTime(0);
		</script>
		</video>
		<?php
		}
	}
	public static function get_random_string($length)
	{
		$random_string="";
		$valid_chars="abcdefghijklmnopqrstuvwxyz1234567890";
		$num_valid_chars=strlen($valid_chars);
		for ($i=0; $i < $length; $i++)
		{
			$random_pick=mt_rand(1, $num_valid_chars);
			$random_char=$valid_chars[$random_pick-1];
			$random_string .= $random_char;
		}
		return $random_string;
	}
	public static function clean($var, $dir = "")
	{
		if(!empty($dir))
		{
			$tmpArray = preg_split("/[\s.]+/",$var);
			$tmpArray = array_filter($tmpArray, create_function('$var',
			'return !(preg_match("/(?:mp4|avi|mkv)|'.
			'(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)'.
			'|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|nSD|WEB|1-PSY|XviD-LOL|REPACK|DL|(?:AC\d)/i", $var));'));
			return implode(" ",$tmpArray);
		}
		$tmpArray = preg_split("/[\s.]+/",$var);
		$tmpArray = array_filter($tmpArray, create_function('$var',
		'return !(preg_match("/(?:mp4|avi|mkv)|'.
		'(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)'.
		'|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|nSD|WEB|1-PSY|XviD-LOL|REPACK|DL|(?:AC\d)/i", $var));'));
		return implode(" ",$tmpArray);
	}
	public static function configInfo($val="")
	{
		$config = @file_get_contents("config/config.json");
		if($config === false){return null;}
		if(empty($val)){ return json_decode($config, true);}
		$config = json_decode($config, true);
		if($val == "ssl"){return $config['ssl'];}
		if($val == "movieDir"){return $config['movieDir'];}
		if($val == "showDir"){return $config['showDir'];}
		if($val == "musicDir"){return $config['musicDir'];}
		if($val == "bitrate"){return $config['bitrate'];}
		
	}
	public static function movieInfo($dir, $val="")
	{
		$dir = escapeshellarg($dir);
		$cmd = "/usr/local/bin/ffprobe -v quiet -print_format json -show_format -show_streams $dir";
		$array = json_decode(shell_exec($cmd),true);
		if(empty($val)){return $array;}
		if($val == "vBitrate")
		{
			if(!empty($array[streams][0]['bit_rate']))
			{
				return round($array[streams][0]['bit_rate']/1024,0);
			}
			else
			{
				if(!empty($array[streams][1]['bit_rate']))
				{
					return round(($array[format]['bit_rate']-$array[streams][1]['bit_rate'])/1024,0);
				}
				else if(!empty($array[format]['bit_rate']))
				{
					return round($array[format]['bit_rate']/1024,0);
				}
				else{return Media::configInfo("bitrate");}
			}
		}
		if($val == "aBitrate"){return round($array[streams][1]['bit_rate']/1024,0);}
		if($val == "vCodec"){return $array[streams][0]['codec_name'];}
		if($val == "aCodec"){return $array[streams][1]['codec_name'];}
		if($val == "length")
		{
			return $array[format]['duration'];
		}
		if($val == "size")
		{
			$cmd = "ls -l  $dir | awk '{print $5}'";
			return shell_exec($cmd);
		}
	}
	public static function playVideo($dir,$time="",$bitrate="")
	{
	
		if(strpos($dir,"shows") === false)
		{
			$dir = "movies/" . html_entity_decode($dir);
		}
		if(!empty($time) && !empty($bitrate))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&time=".$time."&br=".$bitrate;
		}
		else if(!empty($time))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&time=".$time;
		}
		else if (!empty($bitrate))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&br=".$bitrate;
		}
		else
		{
			$urldir = "transcode.php?media=".urlencode($dir);
		}
		$length = Media::movieInfo($dir,"length");
		Media::videojsScripts();
		$br = Media::configInfo("bitrate");
		$mBr = Media::movieInfo($dir,"vBitrate");
		if(!empty($br) && $br <= $mBr){$maxBR = $br;}
		else{$maxBR = $mBr;}
		?>
		<select class="list1" onchange="changebr(<?php print Media::movieInfo($dir,"length");?>)">
		<?php
		for($i = 256; $i <= $maxBR; $i=$i*2)
		{
			if($i < $maxBR)
			{
			?>
			<option value="<?php print $i;?>"><?php print $i;?> kbps</option>
			<?php
			}
			if($i*2 > $maxBR || $i == $maxBR)
			{
			?>
				<option value="<?php print $maxBR;?>" selected><?php print $maxBR;?> kbps</option>
			<?php
			}	
		}
		?>
		</select>
		<?php
		Media::videojs($urldir, 640, 360, $length,$time);
	}
}
?>