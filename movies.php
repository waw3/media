<?php
require "vendor/autoload.php";
class movies
{
	function __construct($array="")
	{
		$movie = urldecode($array[0]);
		$time = $array[1];
		$this->printPage($movie,$time);
	}
	
	function printPage($movie="",$time="")
	{
		$Core = new Core();
		$Core->requireSSL();
		$cwd = $Core->cwd();
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
		$movieSelected = false;
		//The variable is set so someone is probably trying to watch a movie
		if(isset($movie) && stripos(implode($files, " "),
		$movie) !== false ) 
		{
			$movieName = $movie;
			$movieinfo = json_decode(file_get_contents("metadata/movies/".$movie.".json"),true);
			$type = $movieinfo['type'];
			$dir = "movies/" . html_entity_decode(movie);
			$Core->createPage($movie);
			$movieSelected=true;
			$movieTitle = $movieinfo['title'];
			$movieRating = $movieinfo['Rating'];
			$moviePlot = $movieinfo['plot'];
			
		}
		else{$Core->createPage("Simple Media Streamer");}

		//loads the videoplayer if $movieSelectedis true.
		if($movieSelected) 
		{
		?>
			<div id="contentWrapper">
				<div id="videocontainer">
					<text style="position: relative; bottom: 5px; left: 45px;
					text-shadow: 5px 3px 5px rgba(0,0,0,0.75);"><?php print $movieTitle; ?></text>
					<?php
					if(isset($time) && is_numeric($time) 
						&& $time < Media::movieInfo("movies/".$movie.$type,"length"))
					{
						
						Media::playVideo($movie.$type,$time);
					}
					else{Media::playVideo($movie.$type);}	
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
							$poster = "$cwd/metadata/movies/".$movieName.'.jpeg';
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
					$movievalue = urlencode(substr($value,0,strlen($value)-4).$movieinfo[0]);
					$title = $movieinfo['title'];
					$title2 = $title;
					
					if(strlen($title) > 17)
					{
						$title = substr($title,0,17) . "...";
					}

					if(file_exists("metadata/movies/$title2".".jpeg"))
					{
						echo '<div id="PosterContainer" onclick='.
						'\'javascript:location.href="movie/'.$movievalue.'"\'>'.PHP_EOL;
						echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.
						$title.'</label><br>'.PHP_EOL;
						echo '<img class="lazy img-responsive" id="posters" alt="'.$title2.
						'" src="'.$cwd.'/images/holder.jpg" data-original="'.
						$cwd.'/metadata/movies/'.$title2.'.jpeg" >'.PHP_EOL;
						echo '</div>'.PHP_EOL;
					}
				}
			}
			echo '</div>'.PHP_EOL;
		}	
		$Core->endPage("lazyload");
	}
}
?>