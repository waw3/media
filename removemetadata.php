<?php
function metadata()
{
	$metaData = glob("metadata/*.txt");
	$files = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
	foreach($metaData as $file)
		{
			$file = substr(basename($file),0,strlen(basename($file))-4); 
			$result = preg_grep("/$file/",$files);
			if(count($result) == 0)
			{
				unlink("metadata/$file.txt");
				unlink("metadata/$file.jpeg");
			}
	}
}
?>