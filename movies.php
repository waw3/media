<?php
error_reporting(0);
include "template.php"; 
$template = new template();
$template->startSessionRestricted();
if($_SERVER['SERVER_PORT'] == '443') { header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
$dir    = 'movies';
$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
if(isset($_POST['searchtext'])) //Sombody is searching for a movie so we are giong to filter the array to meet their needs.
{
	if(!empty($_POST['searchtext']))
	{
		$files = array_filter($files, function ($var) { $search = $_POST['searchtext']; return (stripos(strtolower($var), strtolower($search)) !== false); });
	}
}

if(isset($_GET["movie"])) //The GET variable is set so someone is probably trying to watch a movie
{
	$type = substr($_GET['movie'],strlen($_GET['movie'])-3);
	if($type == "mkv"){ $type=""; $type = "mp4"; }
	$plainTextMovieName = substr($_GET['movie'],0,strlen($_GET['movie'])-4);
	$movieinfo = file_get_contents("metadata/".$plainTextMovieName.".txt");
	$movieinfo = explode("<br>",$movieinfo);
	$dir = $dir . "/" . html_entity_decode($_GET['movie']);
	$template->createPage($plainTextMovieName);
	$get=true;
	$title = urlencode($plainTextMovieName);
	$movieTitle = $movieinfo[0];
	$movieRating = $movieinfo[1];
	$moviePlot = $movieinfo[3];
}
else
{
	$template->createPage("Simple Media Streamer");
	$template->loadBar();
}

		//echo $template->compareDate($template->secondsToTime($_SESSION['activity'])).PHP_EOL;
if($get) //loads the videoplayer if $get is true.
{ 
	$template->videojsScripts();
	if($template->isMobile())
	{
		echo '<center>'.PHP_EOL;
		$template->videojs($dir, 320,180,"video/$type");
		echo '</center>'.PHP_EOL;
	}
	else
	{
		echo '<div id="contentWrapper">'.PHP_EOL;
		echo '<div id="videocontainer">'.PHP_EOL;
		if($movieTitle == "No information") { $movieTitle = $plainTextMovieName; }
		echo '<p style="margin-bottom: 10px; text-align: center; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$movieTitle.'</p>'.PHP_EOL;
		$height = 360;
		$width = 640;
		if($template->isMobile()) { $height = 180; $width = 320; }
		$template->videojs($dir, $width, $height,"video/$type");
		echo '<p style="text-align: left;text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$moviePlot.'</p>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<div class="metadataContainer">'.PHP_EOL;
		$poster = "movie";
		if(file_exists("metadata/$plainTextMovieName.jpeg")) { $poster = $plainTextMovieName; }
		echo '<img src="metadata/'.$poster.'.jpeg"  id="posters" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
		echo "<p style=\"margin-top: 5px;text-align: center;text-shadow: 5px 3px 5px rgba(0,0,0,0.75); \">$movieRating</p>".PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
	}
} 		
else // if get is false then we load the movie list.
{
	$filesAdded = file_get_contents("Logs/addedContent.log");
	$filesAdded = explode("\n",$filesAdded);
	$count = 1;
	$recentlyAddedContent = array();
	for($i = count($filesAdded)-1; $i >= 0; $i--)
	{
		if(!empty($filesAdded[$i]))
		{
			array_push($recentlyAddedContent,$filesAdded[$i]);
			if($count == 15){ break;}
			$count +=1;
		}
	}
	// We shall load the recently added content first.
	echo '<h1>Recently Added</h1>'.PHP_EOL;
	echo '<div id="recentlyAddedWrapper" style="text-align: center;" >'.PHP_EOL;
	foreach($recentlyAddedContent as $value) //Loop through all the movies detected in the addedContent file.
	{ 
		$getvalue = urlencode($value);
		$movies = file_get_contents("metadata/".substr($value,0,strlen($value)-5).".txt");
		$title = substr($value,0,strlen($value)-4);
		$title2 = $title;
		if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
		if($movies == "No information") { $movies = $title2; }
		echo '<div id="moviePosterContainer" style="margin-top: 10px;" onclick=\'javascript:location.href="/media/movies.php?movie='.$getvalue.'"\'>'.PHP_EOL;
		echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
		if(file_exists("metadata/$title2.jpeg"))
		{
			echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/$title2".'.jpeg" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
		}
		else 
		{
			echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/movie".'.jpeg" width="'.$width.'" height="'.$height.'"\'>'; 
		}
		echo '</div>'.PHP_EOL;
	}
	echo '</div>'.PHP_EOL;	
	echo '<h1>Movies</h1>'.PHP_EOL;	
	echo '<div id="movieWrapper">'.PHP_EOL;				 
	foreach($files as $value) 
	{ 
		$value = basename($value);
		$getvalue = urlencode($value);
		$movies = file_get_contents("metadata/".substr($value,0,strlen($value)-4).".txt");
		$title = substr($value,0,strlen($value)-4);
		$title2 = substr($value,0,strlen($value)-4);
		$value = urlencode(substr($value,0,strlen($value)-4));
		if(strlen($title) > 17)
		{
			$title = substr($title,0,17) . "...";
		}

		if($movies == "No information")
		{
			$movies = $title2;
		}
		echo '<div id="moviePosterContainer" onclick=\'javascript:location.href="/media/movies.php?movie='.$getvalue.'"\'>'.PHP_EOL;
		echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
		if(file_exists("metadata/$title2.jpeg"))
		{
			echo '<img   id="posters" alt="'.$title2.'"  src="'."metadata/$title2".'.jpeg" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
		}
		else
		{
			echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/movie".'.jpeg" width="'.$width.'" height="'.$height.'"\'>';
		}
		echo '</div>'.PHP_EOL;
	}
	echo '</div>'.PHP_EOL;
}	
$template->endPage(); ?>