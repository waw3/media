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
	$movieTitle = $movieinfo[1];
	$movieRating = $movieinfo[2];
	$moviePlot = $movieinfo[4];
	
}
else{$core->createPage("Simple Media Streamer","searchBar","movies.php");}

//loads the videoplayer if $get is true.
if($get) 
{
?>
	<div id="contentWrapper" >
		<div id="videocontainer" style="margin-top: 50px;">
			<text style="position: relative; bottom: 5px; left: 45px;
			text-shadow: 5px 3px 5px rgba(0,0,0,0.75);"><?php print $movieTitle; ?></text>
			<?php
			if(isset($_GET['time']) && is_numeric($_GET['time']) && $_GET['time'] < $core->movieInfo("movies/".$_GET['movie'],"length"))
			{
				$core->playVideo($_GET['movie'],$_GET['time']);
			}
			else{$core->playVideo($_GET['movie']);}

			
			?>
			<p style="text-align: left; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">
			<?php print $moviePlot; ?></p>
		</div>
	
		<div class="metadataContainer">
			<?php
			$poster = "images/movie";
			if(file_exists("metadata/movies/$plainTextMovieName.jpeg")) 
			{ 
				$poster = "metadata/movies/".$plainTextMovieName;
			}
			?>
			<img src="<?php print $poster.'.jpeg'; ?>"  id="posters" 
			width="<?php print $width; ?>" height="<?php print $height; ?>">
			<p style="margin-top: 5px; text-align: center; 
			text-shadow: 5px 3px 5px rgba(0,0,0,0.75); ">
			<?php print $movieRating; ?></p>
			
		</div>
	</div>
<?php	
} 		
else // if get is false then we load the movie list.
{
	echo '<h1 style="margin-top: 50px;">Movies('.
	count($files).')</h1>'.PHP_EOL;	
	echo '<div style="text-align: center;">'.PHP_EOL;				 
	foreach($files as $value) 
	{
		$value = basename($value);
		$movieinfo = @file_get_contents("metadata/movies/".
		substr($value,0,strlen($value)-4).".txt");
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
				'\'javascript:location.href="movies.php?movie='.
				$getvalue.'"\'>'.PHP_EOL;
				echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.
				$title.'</label><br>'.PHP_EOL;
				echo '<img class="lazy img-responsive" id="posters" alt="'.$title2.
				'" src="images/holder.jpg" data-original="'.
				"metadata/movies/$title2".'.jpeg" >'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			}
		}
	}
	echo '</div>'.PHP_EOL;
}	
$core->endPage("lazyload"); 
?>