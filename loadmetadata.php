<?php
include "template.php"; 
$template = new template();
$template->startSessionAdmin();
ini_set("max_execution_time", 1800);
$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
$f = fopen("Logs/addedContent.log", 'a');
$cLog = fopen("Logs/sms-content.log", 'a');
$dbLog = fopen("Logs/db.log", 'a');
$newContent = false;
foreach($files as $value)
{
$newContent = false;
	$value = basename($value);
	$title = substr($value,0,strlen($value)-4);
	
	if(!(strpos($title," ") === false)) { $getvalue = urlencode($title); }
	else { $getvalue = $title; }
	if(!file_exists("metadata/$title.jpeg")) //If the picture doesn't exist then search database for it.
	{
		$newContent = true;
		$json=file_get_contents("http://www.omdbapi.com/?t=$getvalue");
		$details=json_decode($json);
		if($details->Response=='True')
		{   
			$image = $details->Poster;
			if(strpos($image,'N/A') === false)
			{
				file_put_contents("metadata/$title".".jpeg",file_get_contents($image));
				$file = str_replace(" ","\ ", $title.".jpeg");
				$logmsg = shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1");	
				fwrite($cLog, "/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1\n");
				fwrite($cLog, "\t".$logmsg."\n");
			}
			else
			{
				$movieinfo = "";
				$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=$getvalue&api_key=4562bc01bb2592ec113b813da74a0f58");
				$details=json_decode($json);
				if($details->total_results >= 1)
				{
					$path = $details->results[0]->poster_path;
					$image = "http://image.tmdb.org/t/p/w150/$path";
					file_put_contents("metadata/$title".".jpeg",file_get_contents($image));
					$file = str_replace(" ","\ ", $title.".jpeg");
					$logmsg = shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1");	
					fwrite($cLog, "/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1\n");
					fwrite($cLog, "\t".$logmsg."\n");
				
				}	
			}
		}
		else
		{
			$movieinfo = "";
			$json=file_get_contents("http://api.themoviedb.org/3/search/movie?query=$getvalue&api_key=4562bc01bb2592ec113b813da74a0f58");
			$details=json_decode($json);
			if($details->total_results >= 1)
			{
				$path = $details->results[0]->poster_path;
				$image = "http://image.tmdb.org/t/p/w150/$path";
				file_put_contents("metadata/$title".".jpeg",file_get_contents($image));
				$file = str_replace(" ","\ ", $title.".jpeg");
				$logmsg = shell_exec("/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1");	
				fwrite($cLog, "/usr/local/bin/convert -size 300x444 metadata/$file -resize 150x222 metadata/$file 2>&1\n");
				fwrite($cLog, "\t".$logmsg."\n");
				
			}	
			
		}
	}
	if(!file_exists("metadata/$title.txt"))
	{
		$newContent = true;
		$movieinfo = "";
		$json=file_get_contents("http://www.omdbapi.com/?t=$getvalue");
		$details=json_decode($json);
		if($details->Response=='True')
		{   $movieinfo = substr($value,strlen($value)-4)."\n";
			$movieinfo = $movieinfo . $details->Title.'('.$details->Year.")\n";
			$movieinfo = $movieinfo . "Rated : ".$details->Rated . "\n";
			$movieinfo = $movieinfo . "Runtime : ".$details->Runtime."\n";
			$movieinfo = $movieinfo . $movieInfo . "Plot : ".$details->Plot."\n";
			file_put_contents("metadata/$title".".txt",$movieinfo);
		}
		else
		{
			$movieinfo = substr($value,strlen($value)-4)."\nNo information";
			file_put_contents("metadata/$title".".txt",$movieinfo);
		}
		if($newContent)
		{
			fwrite($f,$value."\n");
		}
	}
}
fclose($f);
fclose($cLog);
fclose($dbLog);
?>