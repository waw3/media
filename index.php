
<?php 
require "vendor/autoload.php";
$core = new core();
$core->startSessionRestricted();
$core->createPage("Simple Media Streamer");
$movieFiles = glob("movies/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
$movieFiles = array_combine($movieFiles, array_map("filemtime", $movieFiles));
arsort($movieFiles);
$movieFiles = array_keys($movieFiles);

?>
<?php
echo '<h1 style="text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">Recently Added</h1>'.PHP_EOL;
echo '<div id="recentlyAddedWrapper">'.PHP_EOL;
$num = 15;
if($num > count($movieFiles))
{
	$num = count($movieFiles);
}
for($i = 0; $i < $num; $i++)
{
	$value = basename($movieFiles[$i]);
	$getvalue = urlencode($value);
	$movieInfo = @file_get_contents("metadata/movies/".substr($value,0,strlen($value)-4).".txt");
	if($movieInfo !== FALSE)
	{
		$title = substr($value,0,strlen($value)-4);
		$title2 = $title;
		if(file_exists("metadata/movies/$title2.jpeg"))
		{
			if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
			if($movieInfo == "No information") { $movieInfo = $title2; }
			echo '<div id="PosterContainer" style="margin-top: 5px;" onclick=\'javascript:location.href="/media/movies.php?movie='.$getvalue.'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/movies/$title2".'.jpeg" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
		}
		echo '</div>'.PHP_EOL;
	}
	else{if(($num + 1) < count($movieFiles)){$num = $num + 1;}}
}
echo '</div>'.PHP_EOL;
?>
<?php $core->endPage(); ?>
