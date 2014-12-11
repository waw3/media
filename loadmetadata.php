<?php
function metadata($num)
{
	$f = fopen("Logs/test$num.log", 'a');
	file_put_contents("Logs/test$num.log","");
	ini_set("max_execution_time", 1800);
	$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
	$fCount = (int)(count($files) / 16);
	if($num == 15)
	{
		$fCount2 = $fCount + ((count($files) % 16));
		$files = array_slice($files,$num*$fCount,$fCount2);
	}
	else
	{
		$files = array_slice($files,$num*$fCount,$fCount);
	}
	foreach($files as $movie)
	{
		// By default false.
		$newContent = false;
		// Saving file type.
		$type = substr($movie,strlen($movie)-4); 
		// Removing file type from the end.
		$movie = substr(basename($movie),0,strlen(basename($movie))-4); 
		// saving original movie name.
		$name = $movie;
		if(!(file_exists("metadata/$name.jpeg") && file_exists("metadata/$name.txt")))
		{
			// generally if there are more periods then it might have a file name not suitable for searching so we shall try and fix that.
			if(strpos($movie,'.') !== false)
			{
				//splitting file from periods.
				$movie = preg_split('/[.]/', $movie);
				// Filtering to remove unwanted content.
				$movie = array_filter($movie, create_function('$var','return !(preg_match("/(?:mp4|avi|mkv)|(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|(?:AC\d)/i", $var));'));
				// Adding it all back together.
				$movie = join(' ', $movie);
			}
			
			// Making string more url friendly.
			$urlMovie = urlencode($movie);
			// Searching database for movie information.
			$json = file_get_contents("http://www.omdbapi.com/?t=$urlMovie");
			$results=json_decode($json);
			// Checking if we received a valid response.
			if($results->Response == 'True')
			{
				$newContent = true;
				$movieinfo = $type."\n";
				$movieinfo = $movieinfo . $results->Title.'('.$results->Year.")\n";
				$movieinfo = $movieinfo . "Rated : ".$results->Rated . "\n";
				$movieinfo = $movieinfo . "Runtime : ".$results->Runtime."\n";
				$movieinfo = $movieinfo . $movieInfo . "Plot : ".$results->Plot."\n";
				file_put_contents("metadata/$name".".txt",$movieinfo);
				// Making sure that there is a poster before saving a file.
				if(!($results->Poster == 'N/A'))
				{
					//We got this far so that means there is new content.
					file_put_contents("metadata/$name".".jpeg",file_get_contents($results->Poster));
					$file = str_replace(" ","\ ", $name.".jpeg");
					shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1");
				}
				// If there is no poster, don't give up hope lets try another database!
				else
				{
					$movieinfo = "";
					$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=$urlMovie&api_key=4562bc01bb2592ec113b813da74a0f58");
					$results=json_decode($json);
					if($results->total_results >= 1)
					{
						$newContent = true;
						$path = $results->results[0]->poster_path;
						$image = "http://image.tmdb.org/t/p/w150/$path";
						file_put_contents("metadata/$name".".jpeg",file_get_contents($image));
					}	
				}
			}
			else
			{
				$movieinfo = "";
				$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=$urlMovie&api_key=4562bc01bb2592ec113b813da74a0f58");
				$results=json_decode($json);
				if($results->total_results >= 1)
				{
					$newContent = true;
					$movieinfo = $type."\n";
					$movieinfo = $movieinfo . $results->results[0]->original_title.'('.substr($results->results[0]->release_date,0,4).")\n";
					$movieinfo = $movieinfo . "Rated : Unknown\n";
					$movieinfo = $movieinfo . "Runtime : Unknown\n";
					$movieinfo = $movieinfo . "Plot : Unknown\n";
					file_put_contents("metadata/$name".".txt",$movieinfo);
					$path = $results->results[0]->poster_path;
					$image = "http://image.tmdb.org/t/p/w150/$path";
					file_put_contents("metadata/$name".".jpeg",file_get_contents($image));
				}	
			}

		}
		
	}
}
?>