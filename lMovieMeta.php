<?php
function metadata($num)
{
	$f = fopen("Logs/server.log",'a');
	ini_set("max_execution_time", 900);
	$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
	//Splitting the array into separate sections.
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
	//checking for metadata for each movie in array section.
	foreach($files as $movie)
	{
		//Saving file type.
		$type = substr($movie,strlen($movie)-4); 
		//Removing file type from the end.
		$movie = substr(basename($movie),0,strlen(basename($movie))-4); 
		//Saving original movie name.
		$name = $movie;
		//If we already have something then skip searching.
		if(!(file_exists("metadata/movies/$name.jpeg") && file_exists("metadata/movies/$name.json")))
		{
			if(strpos($movie,'.') !== false && strpos($movie,'. ') === false)
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
			$movieinfo = array();
			$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=".
			"$urlMovie&api_key=4562bc01bb2592ec113b813da74a0f58");
			$results=json_decode($json);
			if($results->total_results >= 1)
			{
				$movieinfo['type'] = $type;
				$movieinfo['title'] = $results->results[0]->original_title;
				$movieinfo['year'] = substr($results->results[0]->release_date,0,4);
				$path = $results->results[0]->poster_path;
				$image = "http://image.tmdb.org/t/p/w150/$path";
				file_put_contents("metadata/movies/$name".".jpeg",file_get_contents($image));
				$file = escapeshellarg(str_replace(" ","\ ", $name.".jpeg"));
				shell_exec("/usr/local/bin/convert -size 300x444 metadata/movies/$file ".
				"-resize 180x266 metadata/movies/$file 2>&1");
				$id = $results->results[0]->id;
				$json = file_get_contents("http://api.themoviedb.org/3/movie/$id"."
				?api_key=4562bc01bb2592ec113b813da74a0f58&append_to_response=releases");
				$results = json_decode($json, true);
				$movieinfo['Rating'] = $results['releases']['countries'][0]['certification'];
				$movieinfo['runtime'] = $results['runtime'];
				$movieinfo['plot'] = $results['overview'];
				file_put_contents("metadata/movies/$name".".json",json_encode($movieinfo));
				fwrite($f,"Matched metadata/movies/$name.jpeg\n");
			}
			else
			{
				fwrite($f,"No match for $movie\n");
			}
		}
	}
	fclose($f);
}
?>