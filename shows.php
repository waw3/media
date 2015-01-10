<?php
require "template.php"; 
$template = new template();
$template->startSessionRestricted();
function play($show,$season = "",$episode, $files)
{
	if(!empty($season))
	{
		$dir = "shows/$show/$season/$episode";
	}
	else
	{
		$dir = "shows/$show/$episode";
	}

	$nIndex = array_search($dir,$files);
	$pIndex = $nIndex;
	$nIndex = $nIndex + 1;
	$pIndex = $pIndex - 1;
	$type = substr($dir,strlen($dir)-3);
	$template = new template();
	$vTranscode = false;
	$aTranscode = false;
	$tTranscode = false;
	$template->videojsScripts();
	$vCount = shell_exec("/usr/local/bin/ffprobe \"$dir\" 2>&1 ".
	"| grep h264 | grep Stream | wc -l");
	$aCount = shell_exec("/usr/local/bin/ffprobe \"$dir\" 2>&1 ".
	"| grep aac | grep Stream | wc -l");
	if($vCount != 1){ $vTranscode = true; }
	if($aCount != 1){ $aTranscode = true; }
	$length = "";
	if($vTranscode)
	{
		$type = "mp4";
		$length = shell_exec("/usr/local/bin/ffmpeg -i \"$dir\" ".
		"2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
		$length = explode(":",$length);
		$length = $length[0]*3600 + $length[1]*60 + $length[2];
		$dir = "transcode.php?show=".urlencode($dir);
	}
	else if($aTranscode)
	{
		$type = "mp4";
		$length = shell_exec("/usr/local/bin/ffmpeg -i \"$dir\" ".
		"2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
		$length = explode(":",$length);
		$length = $length[0]*3600 + $length[1]*60 + $length[2];
		$dir = "transcode.php?show=".urlencode($dir);
		
	}
		echo '<div id="contentWrapper">'.PHP_EOL;
		echo '<div id="videocontainer">'.PHP_EOL;
		$height = 360;
		$width = 640;
		echo '<p style="margin-bottom: 10px; text-align: center;'.
		' text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.
		$template->clean(substr($_GET['episode'],0,
		strlen($_GET['episode'])-4)).'</p>'.PHP_EOL;
		if($template->isMobile()) { $height = 180; $width = 320; }
		if(!empty($length))
		{ 
			$template->videojs($dir, $width, $height, $type, $length);
		}
		else 
		{ 
			$template->videojs($dir, $width, $height, $type); 
		}
		if(!empty($season))
		{
			$episode = basename($files[$pIndex]);
			$episode2 = basename($files[$nIndex]);
			$pDir = urlencode($show)."&season=".urlencode($season)."&episode=".
			urlencode($episode);
			$nDir = urlencode($show)."&season=".urlencode($season)."&episode=".
			urlencode($episode2);
			
		}
		else
		{
			$pDir = urlencode($show)."&episode=".urlencode(basename($files[$pIndex]));
			$nDir = urlencode($show)."&episode=".urlencode(basename($files[$nIndex]));
		}
		$dir = urlencode($dir);
		if($pIndex >= 0)
		{
			print "<button type=\"button\" id=\"button\" style=\"background:".
			" none; float: left;\" onclick=\"javascript:location.href='shows.php".
			"?show=$pDir'\">Previous</button>";
		}
		if($nIndex < count($files))
		{
			print "<button type=\"button\" id=\"button\" style=\"background:".
			" none; float: right;\" onclick=\"javascript:location.href='shows.php?".
			"show=$nDir'\">Next</button>";
		}
		echo '</div>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		
}
$template->createPage("Shows");
if(empty($_GET['show']))
{
	$dirs = array_filter(glob('shows/*'), 'is_dir');
	sort($dirs, SORT_NATURAL);
	for($i = 0; $i < count($dirs); $i++)
	{
		$show = $dirs[$i];
		$shows[basename($show)] = array_filter(glob("$show/*"), 'is_dir');
	}
	echo '<h1>Shows('.count($shows).')</h1>'.PHP_EOL;	
	echo '<div style="text-align: center;">'.PHP_EOL;
	foreach (array_keys($shows) as $showName)
	{
		$fileName = $showName;
		$show = urlencode($showName);
		$showName = $template->clean($showName, "dir");
		if(strlen($showName) > 17)
		{
			$showName = substr($showName,0,17) . "...";
		}
		echo '<div id="PosterContainer" onclick='.
		'\'javascript:location.href="/media/shows.php?show='
		.$show.'"\'>'.PHP_EOL;
		echo '<label style="cursor:pointer; text-shadow: '.
		'5px 3px 5px rgba(0,0,0,0.75);">'.
		$showName.'</label><br>'.PHP_EOL;
		echo '<img class="lazy img-responsive" id="posters" alt="'.$showName.
		'" src="images/holder.jpg" data-original="'.
		"metadata/shows/$fileName".'.jpeg" >'.PHP_EOL;
		echo '</div>'.PHP_EOL;
	}
		echo '</div>'.PHP_EOL;
}
else
{
	if(!empty($_GET['season']) && !empty($_GET['episode']))
	{
		$show = $_GET['show'];
		$season = $_GET['season'];
		$episode = $_GET['episode'];
		$files = glob("shows/$show/$season/*.{mp4,mkv,avi}",GLOB_BRACE );
		sort($files, SORT_NATURAL);
		play($show,$season,$episode,$files);
	}
	else if(!empty($_GET['season']))
	{
		$show = $_GET['show'];
		$urlShow = urlencode($_GET['show']);
		print "<a href=\"shows.php?show=$urlShow\">Back</a><br>"; 
		$season = $_GET['season'];

		$files = glob("shows/$show/$season/*.{mp4,mkv,avi}",GLOB_BRACE );
		sort($files, SORT_NATURAL);
		$season = urlencode($season);
		foreach ($files as $episode)
		{
			$episode = basename($episode);
			$type = substr($_GET['episode'],strlen($_GET['episode'])-4);
			$name = $template->clean($episode);
			$urlEpisode = urlencode($episode.$type);
			if(strlen($name) > 50)
			{
				$name = substr($name,0,50) . "...";
			}		
			echo "<div id=\"musiclist\" onclick='javascript:location.href=\"".
			"shows.php?show=$urlShow&season=$season&episode=$urlEpisode\"'>".PHP_EOL;
			echo "$name";
			echo "</div>".PHP_EOL;
		}
	}
	else if(!empty($_GET['episode']))
	{
		$show = $_GET['show'];
		$episode = $_GET['episode'];
		$files = glob("shows/$show/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
		sort($files, SORT_NATURAL);
		play($show,"",$episode,$files);
	}
	else
	{
		$show = $_GET['show'];
		$urlShow = urlencode($_GET['show']);
		print "<a href=\"shows.php\">Back</a><br>"; 
		$dirs = array_filter(glob("shows/$show/*"), 'is_dir');
		sort($dirs, SORT_NATURAL);
		if(count($dirs) == 0)
		{
			$files = glob("shows/$show/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
			sort($files, SORT_NATURAL);
			foreach ($files as $episode)
			{
				$episode = urlencode(basename($episode));
				$name = $template->clean($episode);
				$urlEpisode = urlencode($episode.$type);
				if(strlen($name) > 50)
				{
					$name = substr($name,0,50) . "...";
				}		
				echo "<div id=\"musiclist\" onclick='javascript:location.href=\"shows.php?".
				"show=$urlShow&episode=$urlEpisode\"'>".PHP_EOL;
				echo "$name";
				echo "</div>".PHP_EOL;
			}
		}
		else
		{
			foreach ($dirs as $season)
			{
				$season = basename($season);
				$getSeason = urlencode(basename($season));
				$name = $template->clean($season, "dir");
				if(strlen($name) > 50)
				{
					$name = substr($name,0,50) . "...";
				}				
				echo "<div id=\"musiclist\" onclick='javascript:location.href=\"shows.php?".
				"show=$urlShow&season=$getSeason\"'>".PHP_EOL;
				echo "$name";
				echo "</div>".PHP_EOL;
			}
		}
	}
}
$template->endPage();
?>