<?php
if($_SERVER['SERVER_PORT'] == '443') 
{ 
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit();
}
require "vendor/autoload.php";
$core = new core();
$core->startSessionRestricted();
function play($show,$season = "",$episode, &$files)
{
	if(!empty($season))
	{
		$dir = "shows/$show/$season/$episode";
	}
	else
	{
		$dir = "shows/$show/$episode";
	}

	$nIndex = $pIndex = array_search($dir,$files);
	$nIndex++;
	$pIndex--;
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
	$core = new core();
	?>
	<div id="contentWrapper">
		<div id="videocontainer" style="margin-top: 50px;">
			<text style="position: relative; bottom: 5px; left: 45px;
			text-shadow: 5px 3px 5px rgba(0,0,0,0.75);"><?php print $core->clean(substr($_GET['episode'],0,strlen($_GET['episode'])-4)); ?></text>
			<?php $core->playVideo($dir);?>
<?php
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
$core->createPage("Shows","searchBar","shows.php");
if(empty($_GET['show']))
{
	$dirs = array_filter(glob('shows/*'), 'is_dir');
	if(isset($_POST['searchtext']))
	{
		// $search = $_POST['searchtext'];[tT][rR][aA]
		// $files = glob("metadata/movies/$search.txt");
		$dirs = array_filter($dirs, 
		function ($var) { 
		$search = $_POST['searchtext']; 
		return (stripos(strtolower($var), strtolower($search)) !== false); 
		});
	}
	sort($dirs, SORT_NATURAL);
	for($i = 0; $i < count($dirs); $i++)
	{
		$show = $dirs[$i];
		$shows[basename($show)] = array_filter(glob("$show/*"), 'is_dir');
	}
	echo '<h1 style="margin-top: 50px;">Shows('.count($shows).')</h1>'.PHP_EOL;	
	echo '<div style="text-align: center;">'.PHP_EOL;
	foreach (array_keys($shows) as $showName)
	{
		if(file_exists("metadata/shows/$showName".".jpeg"))
		{
			$fileName = $showName;
			$show = urlencode($showName);
			$showName = $core->clean($showName, "dir");
			if(strlen($showName) > 17)
			{
				$showName = substr($showName,0,17) . "...";
			}
			echo '<div id="PosterContainer" onclick='.
			'\'javascript:location.href="shows.php?show='
			.$show.'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: '.
			'5px 3px 5px rgba(0,0,0,0.75);">'.
			$showName.'</label><br>'.PHP_EOL;
			echo '<img class="lazy img-responsive" id="posters" alt="'.$showName.
			'" src="images/holder.jpg" data-original="'.
			"metadata/shows/$fileName".'.jpeg" >'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		}
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
		$season = $_GET['season'];

		$files = glob("shows/$show/$season/*.{mp4,mkv,avi}",GLOB_BRACE );
		sort($files, SORT_NATURAL);
		$season = urlencode($season);
		echo '<h1 style="margin-top: 50px;">'.$show.'</h1>'.PHP_EOL;
		echo "<div id=\"musiclist\" onclick='javascript:location.href=\"shows.php?show=$urlShow\"'>".PHP_EOL;
		echo "Back";
		echo '</div>';
		
		foreach ($files as $episode)
		{
			$episode = basename($episode);
			$type = substr($_GET['episode'],strlen($_GET['episode'])-4);
			$name = $core->clean($episode);
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
		$dirs = array_filter(glob("shows/$show/*"), 'is_dir');
		sort($dirs, SORT_NATURAL);
		echo '<h1 style="margin-top: 50px;">'.$show.'</h1>'.PHP_EOL;
		echo "<div id=\"musiclist\" onclick='javascript:location.href=\"shows.php\"'>".PHP_EOL;
		echo "Back";
		echo '</div>';
		if(count($dirs) == 0)
		{
			$files = glob("shows/$show/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
			sort($files, SORT_NATURAL);

			foreach ($files as $episode)
			{
				$episode = urlencode(basename($episode));
				$name = $core->clean($episode);
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
				$name = $core->clean($season, "dir");
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
$core->endPage("lazyload");
?>