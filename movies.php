<?php
if($_SERVER['SERVER_PORT'] == '443') 
{ 
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit();
}
require "vendor/autoload.php";
$core = new core();
$core->startSessionRestricted();
if($core->getBrowser() != "Firefox")
{
	$files = glob("movies/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
}
else
{
	$files = glob("movies/*.{mp4,avi,MP4,AVI}",GLOB_BRACE );
}
natcasesort($files);
$get=false;
//Somebody is searching for a movie.
//So we are going to filter the array to meet their needs.
if(isset($_POST['searchtext']))
{
		// $search = $_POST['searchtext'];[tT][rR][aA]
		// $files = glob("metadata/movies/$search.txt");
		$files = array_filter($files, 
		function ($var) { 
		$search = $_POST['searchtext']; 
		return (stripos(strtolower($var), strtolower($search)) !== false); 
		});
}

//The GET variable is set so someone is probably trying to watch a movie
if(isset($_GET["movie"]) && stripos(implode($files, " "),
substr($_GET["movie"],0,strlen($_GET["movie"])-4)) !== false ) 
{
	$type = substr($_GET['movie'],strlen($_GET['movie'])-3);
	$plainTextMovieName = substr($_GET['movie'],0,strlen($_GET['movie'])-4);
	$movieinfo = file_get_contents("metadata/movies/".$plainTextMovieName.".txt");
	$movieinfo = explode("\n",$movieinfo);
	$dir = "movies/" . html_entity_decode($_GET['movie']);
	$core->createPage($plainTextMovieName);
	$get=true;
	$title = urlencode($plainTextMovieName);
	$movieTitle = $movieinfo[1];
	$movieRating = $movieinfo[2];
	$moviePlot = $movieinfo[4];
}
else
{
	$core->createPage("Simple Media Streamer","searchBar","movies.php");
}
if($get) //loads the videoplayer if $get is true.
{
	$vTranscode = false;
	$aTranscode = false;
	$tTranscode = false;
	$vCount = shell_exec("/usr/local/bin/ffprobe \"$dir\" 2>&1 | grep h264 | grep Stream | wc -l");
	
	$aCount = shell_exec("/usr/local/bin/ffprobe \"$dir\" 2>&1 | grep aac | grep Stream | wc -l");
	if($vCount != 1){ $vTranscode = true; }
	if($aCount != 1){ $aTranscode = true; }
	if($type != "mp4") { $tTranscode = true; }
	$length = "";
	if($core->getBrowser() == "Firefox" && $tTranscode == 1)
	{
		$type = "webm";
		$length = shell_exec("/usr/local/bin/ffmpeg -i \"$dir\" 2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
		$length = explode(":",$length);
		$length = $length[0]*3600 + $length[1]*60 + $length[2];
		$dir = "transcode.php?media=".urlencode(basename($dir));
	}
	else if($vTranscode)
	{
		
		$type = "mp4";
		$length = shell_exec("/usr/local/bin/ffmpeg -i \"$dir\" 2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
		$length = explode(":",$length);
		$length = $length[0]*3600 + $length[1]*60 + $length[2];
		$dir = "transcode.php?media=".urlencode(basename($dir));
		
	}
	else if($aTranscode)
	{
		$type = "mp4";
		$length = shell_exec("/usr/local/bin/ffmpeg -i \"$dir\" 2>&1 | grep Duration | awk '{print $2}' | sed 's/...,//'");
		$length = explode(":",$length);
		$length = $length[0]*3600 + $length[1]*60 + $length[2];
		$dir = "transcode.php?media=".urlencode(basename($dir));
	}
	
	$core->videojsScripts();
	echo '<div id="contentWrapper">'.PHP_EOL;
	echo '<div id="videocontainer">'.PHP_EOL;
	if($movieTitle == "No information") 
	{ 
		$movieTitle = $plainTextMovieName; 
	}
	echo '<p style="margin-bottom: 10px; text-align: center;'.
	' text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$movieTitle.'</p>'.PHP_EOL;
	$height = 360;
	$width = 640;
	if($core->isMobile()) { $height = 180; $width = 320; }
	if(!empty($length))
	{ 
			$core->videojs($dir, $width, $height, $length);
	}
	else
	{ 
		$core->videojs($dir, $width, $height); 
	}
	echo '<p style="text-align: left;'.
	'text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$moviePlot.'</p>'.PHP_EOL;
	echo '</div>'.PHP_EOL;
	echo '<div class="metadataContainer">'.PHP_EOL;
	$poster = "images/movie";
	if(file_exists("metadata/movies/$plainTextMovieName.jpeg")) 
	{ 
		$poster = "metadata/movies/".$plainTextMovieName;
	}
	echo '<img src="'.$poster.'.jpeg"  id="posters" '.
	'width="'.$width.'" height="'.$height.'">'.PHP_EOL;
	echo "<p style=\"margin-top: 5px;text-align: center;".
	"text-shadow: 5px 3px 5px rgba(0,0,0,0.75); \">$movieRating</p>".PHP_EOL;
	echo '</div>'.PHP_EOL;
	echo '</div>'.PHP_EOL;	
} 		
else // if get is false then we load the movie list.
{	
	
	echo '<h1 style="margin-top: 50px;">Movies('.count($files).')</h1>'.PHP_EOL;	
	echo '<div style="text-align: center;">'.PHP_EOL;				 
	foreach($files as $value) 
	{
		$value = basename($value);
		$movieinfo = @file_get_contents("metadata/movies/".substr($value,0,strlen($value)-4).".txt");
		if($movieinfo !== FALSE)
		{
			$movieinfo = explode("\n",$movieinfo);
			$getvalue = urlencode(substr($value,0,strlen($value)-4).$movieinfo[0]);
			$title = substr($movieinfo[1],0,strpos($movieinfo[1],"("));
			$title2 = substr($value,0,strlen($value)-4);
			
			if(strlen($title) > 17)
			{
				$title = substr($title,0,17) . "...";
			}
			if(file_exists("metadata/movies/$title2".".jpeg"))
			{
				echo '<div id="PosterContainer" onclick='.
				'\'javascript:location.href="movies.php?movie='.$getvalue.'"\'>'.PHP_EOL;
				echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.
				$title.'</label><br>'.PHP_EOL;
				echo '<img class="lazy img-responsive" id="posters" alt="'.$title2.
				'" src="images/holder.jpg" data-original="'."metadata/movies/$title2".'.jpeg" >'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			}
		}
	}
	echo '</div>'.PHP_EOL;
}	
$core->endPage("lazyload"); 
?>