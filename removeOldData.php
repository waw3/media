<?php
function checkData()
{
	$shows = array_filter(glob('shows/*'), 'is_dir');
	$metaShows = glob("metadata/shows/*.txt");
	$movies = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
	$metaMovies = glob("metadata/movies/*.txt");
	cleanArray($shows);
	cleanArray($metaShows);
	cleanArray($movies);
	cleanArray($metaMovies);
	$diff = array_diff($metaMovies,$movies);
	foreach($diff as $data)
	{
		unlink("metadata/movies/".$data.".jpeg");
		unlink("metadata/movies/".$data.".txt");
	}
	$diff2 = array_diff($metaShows,$shows);
	foreach($diff2 as $data)
	{
		unlink("metadata/shows/".$data.".jpeg");
		unlink("metadata/shows/".$data.".txt");
	}
}
function cleanArray(&$array)
{
	for($i = 0; $i < count($array); $i++)
	{
		if(strpos($array[$i],"txt") !== false 
		|| strpos($array[$i],"movies") !== false)
		{
			$array[$i] = basename($array[$i]);
			$array[$i] = substr($array[$i],0,count($array[$i])-5);
		}
		else
		{
			$array[$i] = basename($array[$i]);
		}
	}

}
?>