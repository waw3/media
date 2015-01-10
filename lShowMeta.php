<?php
function metadata($num)
{
	require "template.php"; 
	$template = new template();
	$f = fopen("Logs/addedShows.log", 'a');
	ini_set("max_execution_time", 1800);
	$files = $dirs = array_filter(glob("shows/*"), 'is_dir');
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
	foreach($files as $show)
	{
		$newContent = false;
		$show = basename($show); 
		$name = $show;
		if(!(file_exists("metadata/shows/$name.jpeg") && file_exists("metadata/shows/$name.txt")))
		{
			$show = $template->clean($show,"dir");

			// Making string more url friendly.
			$urlMovie = urlencode($show);
			// Searching database for movie information.
				$showinfo = "";
				$json=file_get_contents("http://api.themoviedb.org/3/search/tv?query=".
				"$urlMovie&api_key=4562bc01bb2592ec113b813da74a0f58");
				$results=json_decode($json);
				if($results->total_results >= 1)
				{
					$showinfo = $results->results[0]->original_title."\n";
					file_put_contents("metadata/shows/$name".".txt",$showinfo);
					$path = $results->results[0]->poster_path;
					$image = "http://image.tmdb.org/t/p/w150/$path";
					file_put_contents("metadata/shows/$name".".jpeg",file_get_contents($image));
					$file = escapeshellarg(str_replace(" ","\ ", $name.".jpeg"));
					shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file ".
					"-resize 180x266 metadata/$file 2>&1");
					$id = $results->results[0]->id;
					$json = file_get_contents("https://api.themoviedb.org/3/tv/$id?api_key=4562bc01bb2592ec113b813da74a0f58&append_to_response=releases");
					$results = json_decode($json, true);
					fwrite($f,$name.$type."\n");
				}	
			}
	}
	fclose($f);
	$lines = file('Logs/addedShows.log');
	$lines = array_unique($lines);
	file_put_contents('Logs/addedShows.log', implode($lines));
}
?>