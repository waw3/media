<?php
require "template.php";
$template = new template();
$template->startSessionRestricted();
if (!empty($_FILES)) 
{
	if(!dir("music/".$_SESSION['username'])) 
	{ 
		mkdir("music/".$_SESSION['username']);
		chown("music/".$_SESSION['username'], 777);
	}
    $tempFile = $_FILES['file']['tmp_name'];               
    $targetPath = '/raid1pool/www/media/music/'.$_SESSION['username']."/".$_FILES['file']['name'];
	fwrite($myfile,$tempFile);
    move_uploaded_file($tempFile,$targetPath); //6   
}
?>   