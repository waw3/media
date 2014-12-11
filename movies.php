<?php
require "template.php"; 
$template = new template();
$template->startSessionRestricted();
$dir    = 'movies';
$files = glob("metadata/*txt");
if(isset($_POST['searchtext'])) //Sombody is searching for a movie so we are giong to filter the array to meet their needs.
{
	if(!empty($_POST['searchtext']))
	{
		$files = array_filter($files, function ($var) { $search = $_POST['searchtext']; return (stripos(strtolower($var), strtolower($search)) !== false); });
	}
}

if(isset($_GET["movie"])) //The GET variable is set so someone is probably trying to watch a movie
{
	if($_SERVER['SERVER_PORT'] == '443') { header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
	$type = substr($_GET['movie'],strlen($_GET['movie'])-3);
	$plainTextMovieName = substr($_GET['movie'],0,strlen($_GET['movie'])-4);
	$movieinfo = file_get_contents("metadata/".$plainTextMovieName.".txt");
	$movieinfo = explode("\n",$movieinfo);
	$dir = $dir . "/" . html_entity_decode($_GET['movie']);
	$template->createPage($plainTextMovieName);
	$get=true;
	$title = urlencode($plainTextMovieName);
	$movieTitle = $movieinfo[1];
	$movieRating = $movieinfo[2];
	$moviePlot = $movieinfo[4];
}
else
{
	$template->createPage("Simple Media Streamer", "customScrollLazyLoad");
}
if($get) //loads the videoplayer if $get is true.
{
	$template->videojsScripts();
	if($type != "mp4")
	{
		echo "<h2>Needs to be transcoded!</h2>".PHP_EOL;
	}
	if($template->isMobile())
	{
		echo '<center>'.PHP_EOL;
		$template->videojs($dir, 320,180);
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
		$template->videojs($dir, $width, $height);
		echo '<p style="text-align: left;text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$moviePlot.'</p>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<div class="metadataContainer">'.PHP_EOL;
		$poster = "images/movie";
		if(file_exists("metadata/$plainTextMovieName.jpeg")) { $poster = "metadata/".$plainTextMovieName; }
		echo '<img src="'.$poster.'.jpeg"  id="posters" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
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
	echo '<div id="recentlyAddedWrapper" >'.PHP_EOL;
	foreach($recentlyAddedContent as $value) //Loop through all the movies detected in the addedContent file.
	{ 
		$getvalue = urlencode($value);
		$movies = file_get_contents("metadata/".substr($value,0,strlen($value)-4).".txt");
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
			echo '<img  id="posters" alt="'.$title2.'" src="'."images/movie".'.jpeg" width="'.$width.'" height="'.$height.'"\'>'; 
		}
		echo '</div>'.PHP_EOL;
	}
	echo '</div>'.PHP_EOL;	
	echo '<h1>Movies('.count($files).')</h1>'.PHP_EOL;	
	echo '<div id="movieWrapper" style="text-align: center;">'.PHP_EOL;				 
	foreach($files as $value) 
	{
		$value = basename($value);
		$movieinfo = file_get_contents("metadata/".$value);
		$movieinfo = explode("\n",$movieinfo);
		$getvalue = urlencode(substr($value,0,strlen($value)-4).$movieinfo[0]);
		$title = substr($value,0,strlen($value)-4);
		$title2 = substr($value,0,strlen($value)-4);
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
			echo '<img  id="posters" alt="'.$title2.'" src="'."images/movie".'.jpeg" width="'.$width.'" height="'.$height.'"\'>';
		}
		echo '</div>'.PHP_EOL;
	}
	echo '</div>'.PHP_EOL;
}	
$template->endPage(); ?>