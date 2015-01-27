<?php
require "vendor/autoload.php";
$Core = new Core();
$Core->requireSSL();
$Core->startSessionRestricted();
if($Core->getBrowser() != "Firefox")
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
		$files = array_filter($files, 
		function ($var) { 
		$search = $_POST['searchtext']; 
		return (stripos(strtolower($var), strtolower($search)) !== false); 
		});
}

//The GET variable is set so someone is probably trying to watch a movie
if(isset($_GET["movie"]) && stripos(implode($files, " "),
$_GET["movie"]) !== false ) 
{
	$movieName = $_GET['movie'];
	$movieinfo = json_decode(file_get_contents("metadata/movies/".$_GET["movie"].".json"),true);
	$type = $movieinfo['type'];
	$dir = "movies/" . html_entity_decode($_GET['movie']);
	$Core->createPage($_GET["movie"]);
	$get=true;
	$movieTitle = $movieinfo['title'];
	$movieRating = $movieinfo['Rating'];
	$moviePlot = $movieinfo['plot'];
	
}
else{$Core->createPage("Simple Media Streamer","searchBar","movies.php");}

//loads the videoplayer if $get is true.
if($get) 
{
?>
	<div id="contentWrapper">
		<div id="videocontainer">
			<text style="position: relative; bottom: 5px; left: 45px;
			text-shadow: 5px 3px 5px rgba(0,0,0,0.75);"><?php print $movieTitle; ?></text>
			<?php
			if(isset($_GET['time']) && is_numeric($_GET['time']) 
				&& $_GET['time'] < Media::movieInfo("movies/".$_GET['movie'].$type,"length"))
			{
				
				Media::playVideo($_GET['movie'].$type,$_GET['time']);
			}
			else{Media::playVideo($_GET['movie'].$type);}	
			?>
			<div id="plot">
			<fieldset style="width:100%; margin-top: 10px;">
			<legend style="color: #E8E8E8;">Plot</legend>
			<?php print $moviePlot; ?></p>
			</fieldset>
			</div>
		</div>
	
		<div class="metadataContainer">
			<div class="info">
				<?php
				$poster = "images/movie.jpeg";
				if(file_exists("metadata/movies/$movieName.jpeg")) 
				{ 
					$poster = "metadata/movies/".$movieName.'.jpeg';
				}
				?>
				<img src="<?php print $poster; ?>"  id="posters" 
				width="<?php print $width; ?>" height="<?php print $height; ?>">
				<p style="margin-top: 5px; text-align: center; 
				text-shadow: 5px 3px 5px rgba(0,0,0,0.75); ">
				<?php print "Rated ".$movieRating; ?></p>
			</div>
			<div id="options" style="text-align: left;">
				<button type="button" id="button" style="margin-left: 5px; background: none;" onclick="popup('report')">Report</button>
			</div>
		</div>
		
	</div>
<?php	
} 		
else // if get is false then we load the movie list.
{
	echo '<h1 id="title">Movies('.count($files).')</h1>'.PHP_EOL;	
	echo '<div style="text-align: center;">'.PHP_EOL;				 
	foreach($files as $value) 
	{
		$value = basename($value);
		$movieinfo = json_decode(@file_get_contents("metadata/movies/".
		substr($value,0,strlen($value)-4).".json"),true);
		if($movieinfo !== FALSE)
		{
			$getvalue = urlencode(substr($value,0,strlen($value)-4).$movieinfo[0]);
			$title = $movieinfo['title'];
			$title2 = $title;
			
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
$Core->endPage("lazyload"); 
?>