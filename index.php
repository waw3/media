<?php 
require "template.php";
$template = new template();
$template->startSessionRestricted();
$template->createPage("Simple Media Streamer", "customScrollLazyLoad");
if(file_exists("Logs/addedContent.log"))
{
	$filesAdded = file_get_contents("Logs/addedContent.log");
		$filesAdded = explode("\n",$filesAdded);
		$count = 1;
		$recentlyAddedContent = array();
		for($i = count($filesAdded)-1; $i >= 0; $i--)
		{
			if(!empty($filesAdded[$i]))
			{
				array_push($recentlyAddedContent,$filesAdded[$i]);
				if($count == 15){ break;}
				$count +=1;
			}
		}
		// We shall load the recently added content first.
		echo '<h1 style="text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">Recently Added</h1>'.PHP_EOL;
		echo '<div id="recentlyAddedWrapper">'.PHP_EOL;
		foreach($recentlyAddedContent as $value) //Loop through all the movies detected in the addedContent file.
		{ 
			$getvalue = urlencode($value);
			$movies = file_get_contents("metadata/".substr($value,0,strlen($value)-4).".txt");
			$title = substr($value,0,strlen($value)-4);
			$title2 = $title;
			if(strlen($title) > 17) { $title = substr($title,0,17) . "..."; }
			if($movies == "No information") { $movies = $title2; }
			echo '<div id="moviePosterContainer" style="margin-top: 5px;" onclick=\'javascript:location.href="/media/movies.php?movie='.$getvalue.'"\'>'.PHP_EOL;
			echo '<label style="cursor:pointer; text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">'.$title.'</label><br>'.PHP_EOL;
			if(file_exists("metadata/$title2.jpeg"))
			{
				echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/$title2".'.jpeg" width="'.$width.'" height="'.$height.'">'.PHP_EOL;
			}
			else 
			{
				echo '<img  id="posters" alt="'.$title2.'" src="'."metadata/movie".'.jpeg" width="'.$width.'" height="'.$height.'"\'>'; 
			}
			echo '</div>'.PHP_EOL;
		}
		echo '</div>'.PHP_EOL;	
}
$template->endPage(); ?>
