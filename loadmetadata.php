<?php
include "template.php"; 
$template = new template();
$template->startSessionAdmin();
ini_set("max_execution_time", 1800);
$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
$f = fopen("Logs/addedContent.log", 'a');
$cLog = fopen("Logs/sms-content.log", 'a');
$newContent = false;
foreach($files as $value) 
{
$newContent = false;
	$value = basename($value);
	$title = substr($value,0,strlen($value)-4);
	$getvalue = urlencode($title);
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
		}
	}
	if(!file_exists("metadata/$title.txt"))
	{
		$newContent = true;
		$movieinfo = "";
		$json=file_get_contents("http://www.omdbapi.com/?t=$getvalue");
		$details=json_decode($json);
		if($details->Response=='True')
		{   
			$movieinfo = $movieinfo . $details->Title.'('.$details->Year.')<br>';
			$movieinfo = $movieinfo . "Rated : ".$details->Rated.'<br>';
			$movieinfo = $movieinfo . "Runtime : ".$details->Runtime.'<br>';
			$movieinfo = $movieinfo . $movieInfo . "Plot : ".$details->Plot.'<br>';
			file_put_contents("metadata/$title".".txt",$movieinfo);
		}
		else
		{
			file_put_contents("metadata/$title".".txt","No information");
		}
		if($newContent){fwrite($f,$value."\n");}
	}
}
fclose($f);
fclose($cLog);
?>
