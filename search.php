<?php
require "vendor/autoload.php"; 
$Core = new Core();
$Core->requireSSL();
$Core->startSessionRestricted();
$Core->createPage("Search", false);
if(!isset($_GET['srch-term'])){$dir=$Core->cwd();header("Location: $dir");}
if($Core->getBrowser() != "Firefox")
{
	$files = glob("movies/*.{mp4,mkv,avi,MP4,MKV,AVI}",GLOB_BRACE );
}
else
{
	$files = glob("movies/*.{mp4,avi,MP4,AVI}",GLOB_BRACE );
}
$shows = array_filter(glob('shows/*'), 'is_dir');
$files = array_merge($files,$shows);
$files = array_filter($files, 
function ($var) { 
	$search = $_GET['srch-term'];
	return (stripos(strtolower($var), strtolower($search)) !== false); 
});
natcasesort($files);
if(count($files) <= 0)
{
?>
	<div class="alert alert-danger" role="alert" style="width: 300px; margin: 0 auto;">
		<span class="glyphicon glyphicon-exclamation-sign"></span>
		No results
	</div>
<?php
exit();
}
foreach($files as $media)
{
	if(strpos($media,"movies") !== false)
	{

		$value = basename($media);
		$title = substr($value,0,strlen($value)-4);
		$getvalue = urlencode($title);
		$title2 = $title;
		if(file_exists("metadata/movies/$title.jpeg"))
		{
			if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
			echo '<div id="PosterContainer" style="margin-top: '.
			'5px; margin-left: 5px;" onclick=\'javascript:location.href="movies.php?movie='.
			$getvalue.'&time='.$cw["$media"][0].'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: '.
			'5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			echo '<img  id="posters" alt="'.$title2.'" src="'.
			"metadata/movies/$title2".'.jpeg"><br>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		}
	}
	else
	{
		$value = basename($media);
		$title = $value;
		$getvalue = urlencode($title);
		$title2 = $title;
		if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
		$title = Media::clean($title);
		$show = str_replace("shows/","",$media);
		$filename = explode("/",$show);
		if(count($filename) == 3)
		{
			$getvalue = urlencode($filename[0])."&season=".
			urlencode($filename[1])."&episode=".urlencode($filename[2]);
		}
		else if(count($filename) == 2)
		{
			$getvalue = urlencode($filename[0])."&episode=".
			urlencode($filename[1]);
		}
		if(file_exists("metadata/shows/$filename[0].jpeg"))
		{
			echo '<div id="PosterContainer" style="margin-top: 5px;" '.
			'onclick=\'javascript:location.href="shows.php?show='.
			$getvalue.'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px '.
			'rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			echo '<img  id="posters" alt="'.$title2.
			'" src="'."metadata/shows/$filename[0]".'.jpeg"><br>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		}
	}
}

$Core->endPage(); ?>
