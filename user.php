<?php 
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
require "template.php";
$template = new template();
$template->startSessionRestricted();
$con = $template->dbConnect();
$template->createPage("User control panel");
$template->endPage(); ?>