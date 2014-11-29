<?php 
session_start();
include "template.php";
$template = new template();
$template->createPage("Simple Media Streamer");
print '<h1 style="text-shadow: 5px 3px 5px rgba(0,0,0,0.75);">Welcome!</h1>'.PHP_EOL;	
$template->endPage(); ?>
