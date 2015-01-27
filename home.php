
<?php 
require "vendor/autoload.php";
$Core = new Core();
$Core->startSessionRestricted();
$Core->createPage("Simple Media Streamer");
if($Core->getBrowser() != "Firefox")
{
	$offset = 0;
	$movieFiles = glob("movies/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
}
else
{
	$offset = 1;
	$movieFiles = glob("movies/*.{mp4,avi,MP4,AVI}",GLOB_BRACE );
}
$movieFiles = array_combine($movieFiles, array_map("filemtime", $movieFiles));
arsort($movieFiles);
$movieFiles = array_keys($movieFiles);

echo '<div id="mainContentWrapper">'.PHP_EOL;
$cw = @file_get_contents("Logs/".$_SESSION['username']."_cw.json");
if($cw !== false && $cw != '[]')
{
	$cw = json_decode($cw,true);
	$name = array_reverse(array_keys($cw));

	echo '<h1 class="currentlyWatching" style="text-shadow: 5px '.
	'3px 5px rgba(0,0,0,0.75); margin-top: 0px;">Currently Watching</h1>'.PHP_EOL;
	echo '<div id="currentlyWatchingWrapper">'.PHP_EOL;
	$cwd = $Core->cwd();
	foreach($name as $media)
	{
		$value = basename($media);
		
		$title = substr($value,0,strlen($value)-4);
		$getvalue = urlencode($title);
		$title2 = $title;
		if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
		if(strpos($media,"movies") !== false)
		{
			echo '<div id="PosterContainer" style="margin-top: '.
			'5px; margin-left: 5px;" onclick=\'javascript:location.href="'.$cwd.'/movie/'.
			$getvalue.'/'.$cw["$media"][0].'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: '.
			'5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			echo '<img  id="posters" alt="'.$title2.'" src="'.
			"$cwd/metadata/movies/$title2".'.jpeg"><br>'.PHP_EOL;
			echo '<progress value="'.$cw["$media"][0].'" max="'.
			$cw["$media"][1].'"></progress>'.PHP_EOL;
			echo '</div>'.PHP_EOL;		
		}
		else
		{
			$title = Media::clean($title);
			$show = str_replace("shows/","",$media);
			
			$filename = explode("/",$show);
			if(count($filename) == 3)
			{
				$getvalue = urlencode($filename[0])."&season=".
				urlencode($filename[1])."&episode=".urlencode($filename[2]);
			}
			else if(count($filename) == 2)
			{
				$getvalue = urlencode($filename[0])."&episode=".
				urlencode($filename[1]);
			}
			echo '<div id="PosterContainer" style="margin-top: 5px;'.
			'margin-left: 5px;" onclick=\'javascript:location.href="'.$cwd.'/shows.php?show='.
			$getvalue.'&time='.$cw["$media"][0].'"\'>'.PHP_EOL;

			echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px '.
			'rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			echo '<img  id="posters" alt="'.$title2.
			'" src="'."$cwd/metadata/shows/$filename[0]".'.jpeg"><br>'.PHP_EOL;
			echo '<progress value="'.$cw["$media"][0].
			'" max="'.$cw["$media"][1].'"></progress>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		}
		
	}
	echo '<button id="scrollbutton" class="currentlyWatching" '.
	'style="top: '.(44+$offset).'px; right: 0;" name="scroll1" '.
	'onclick="scrollX(currentlyWatchingWrapper,400)">&#9658;</button>';
	echo '<button id="scrollbutton" class="currentlyWatching" '.
	'style="top: '.(44+$offset).'px; left: 0;" name="scroll1" '.
	'onclick="scrollX(currentlyWatchingWrapper,-400)">&#9668;</button>';
	echo '</div>'.PHP_EOL;	
}
if(count($movieFiles) > 0)
{
	echo '<h1 class="recentMovies" style="text-shadow: 5px 3px 5px rgba(0,0,0,0.75);'.
	' margin-top: 50px;">Recently Added Movies</h1>'.PHP_EOL;
	echo '<div id="recentlyAddedMovies" style="margin-bottom: 50px;">'.PHP_EOL;
	$num = 25;
	if($num > count($movieFiles))
	{
		$num = count($movieFiles);
	}
	for($i = 0; $i < $num; $i++)
	{
		$value = basename($movieFiles[$i]);
		$value = substr($value,0,strlen($value)-4);
		$getvalue = urlencode($value);
		$movieInfo = json_decode(@file_get_contents("metadata/movies/".
		$value.".json"),true);
		if(count($movieInfo) > 0)
		{
			$title = $value;
			$title2 = $title;

			if(file_exists("metadata/movies/$title2.jpeg"))
			{
				if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
				if($movieInfo == "No information") { $movieInfo = $title2; }
				echo '<div id="PosterContainer" style="margin-top: 5px; margin-left:'.
				' 5px;" onclick=\'javascript:location.href="'.$cwd.'/movies.php?movie='
				.$getvalue.'"\'>'.PHP_EOL;
				echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px '.
				'rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
				echo '<img  id="posters" alt="'.$title2.'" src="'.
				"$cwd/metadata/movies/$title2".'.jpeg">'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			}
		}
		else{if(($num + 1) < count($movieFiles)){$num = $num + 1;}}
	}
	echo '</div>'.PHP_EOL;
	if($cw === false || $cw == '[]')
	{
		echo '<button id="scrollbutton" class="recentMovies" "top: '.(44+$offset).'px; right: 0;" '.
		'name="scroll2" onclick="scrollX(recentlyAddedMovies,400)">&#9658;</button>';
		echo '<button id="scrollbutton" class="recentMovies" "top: '.(44+$offset).'px; left: 0;" '.
		'name="scroll2" onclick="scrollX(recentlyAddedMovies,-400)">&#9668;</button>';
	}
	else
	{
		echo '<button id="scrollbutton" class="recentMovies" style="top: '.(448+$offset).'px; right: 0;" '.
		'name="scroll2" onclick="scrollX(recentlyAddedMovies,400)">&#9658;</button>';
		echo '<button id="scrollbutton" class="recentMovies" style="top: '.(448+$offset).'px; left: 0;" '.
		'name="scroll2" onclick="scrollX(recentlyAddedMovies,-400)">&#9668;</button>';
	}
	
	echo '</div>'.PHP_EOL;
	
}
?>
<script>
var x = document.getElementsByName("scroll2");
var x2 = document.getElementsByName("scroll1");
var r = document.getElementById("recentlyAddedMovies");
var c = document.getElementById("currentlyWatchingWrapper");
var height2 = c.offsetHeight;
var height = r.offsetHeight;
x[0].style.height = height+"px";
x[1].style.height = height+"px";
x2[0].style.height = height2+"px";
x2[1].style.height = height2+"px";
</script>
<?php $Core->endPage(); ?>
