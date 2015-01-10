<?php
function metadata($num)
{
	$f = fopen("Logs/addedMovies.log", 'a');
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
		if(!(file_exists("metadata/movies/$name.jpeg") && file_exists("metadata/movies/$name.txt")))
		{
			if(strpos($movie,'.') !== false)
			{
				//splitting file from periods.
				$movie = preg_split('/[.]/', $movie);
				// Filtering to remove unwanted content.
				$movie = array_filter($movie, create_function('$var',
				'return !(preg_match("/(?:mp4|avi|mkv)|'.
				'(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)'.
				'|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|(?:AC\d)/i", $var));'));
				// Adding it all back together.
				$movie = join(' ', $movie);
			}
			
			// Making string more url friendly.
			$urlMovie = urlencode($movie);
			// Searching database for movie information.
				$movieinfo = "";
				$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=".
				"$urlMovie&api_key=4562bc01bb2592ec113b813da74a0f58");
				$results=json_decode($json);
				if($results->total_results >= 1)
				{
					$movieinfo = $type."\n";
					$movieinfo = $movieinfo . $results->results[0]->original_title.
					'('.substr($results->results[0]->release_date,0,4).")\n";
					file_put_contents("metadata/movies/$name".".txt",$movieinfo);
					$path = $results->results[0]->poster_path;
					$image = "http://image.tmdb.org/t/p/w150/$path";
					file_put_contents("metadata/movies/$name".".jpeg",file_get_contents($image));
					$file = escapeshellarg(str_replace(" ","\ ", $name.".jpeg"));
					shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file ".
					"-resize 180x266 metadata/$file 2>&1");
					$id = $results->results[0]->id;
					$json = file_get_contents("https://api.themoviedb.org/3/movie/$id?api_key=4562bc01bb2592ec113b813da74a0f58&append_to_response=releases");
					$results = json_decode($json, true);
					$runtime = $results['runtime'];
					$plot = $results['overview'];
					$rating = $results['releases']['countries'][0]['certification'];
					$movieinfo = $movieinfo . "Rated : $rating\n";
					$movieinfo = $movieinfo . "Runtime : $runtime\n";
					$movieinfo = $movieinfo . "Plot : $plot";
					file_put_contents("metadata/movies/$name".".txt",$movieinfo);
					fwrite($f,$name.$type."\n");
				}	
			}
	}
	fclose($f);
	$lines = file('Logs/addedMovies.log');
	$lines = array_unique($lines);
	file_put_contents('Logs/addedMovies.log', implode($lines));
}
?>