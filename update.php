<?php
require "vendor/autoload.php";
$Core = new Core();
$Core->startSessionAdmin();
$shows = array_filter(glob('shows/*'), 'is_dir');
$metaShows = glob("metadata/shows/*.json");
$movies = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
$metaMovies = glob("metadata/movies/*.json");
cleanArray($shows);
cleanArray($metaShows);
cleanArray($movies);
cleanArray($metaMovies);
$count = shell_exec('pgrep -f lMovieMeta | wc -l');
if($count < 2)
{
	for($i = 0; $i < 16; $i++)
	{
		exec('/usr/local/bin/php -r "require \'lMovieMeta.php\'; metadata('.$i.');" > /dev/null &');
	}
	exec('/usr/local/bin/php -r "require \'removeOldData.php\'; checkData();" > /dev/null &');
	$count = shell_exec('pgrep -f lMovieMeta | wc -l');
	?>
<?php
}
else if($count > 2)
{
?>
	<script>
	setInterval(function() {
		location.reload();
	}, 1000);
	</script>
<?php
}

$Core->createPage("Simple Media Streamer");

print '<h1 style="margin-top: 50px;">Searching</h1>';
$unmatch = 0;
$match = 0;
foreach($movies as $movie)
{
	if(array_search($movie,$metaMovies) === false)
	{
		$unmatch++;
		print $movie."<br>";
	}
	else{$match++;}
	
}
echo '<div style="position: absolute; right: 50px; top: 50px;">'.PHP_EOL;
	echo '<p>Matched Files: '.$match.'</p>';
	echo '<p>Unmatched Files: '.$unmatch.'</p>';
	echo '</div>'.PHP_EOL;
$Core->endPage();
function cleanArray(&$array)
{
	for($i = 0; $i < count($array); $i++)
	{
		if(strpos($array[$i],"json") !== false)
		{
			$array[$i] = basename($array[$i]);
			$array[$i] = substr($array[$i],0,count($array[$i])-6);
		}
		else if(strpos($array[$i],"movies") !== false)
		{
			$array[$i] = basename($array[$i]);
			$array[$i] = substr($array[$i],0,count($array[$i])-5);
			
		}
		else
		{
			$array[$i] = basename($array[$i]);
		}
	}

}?>