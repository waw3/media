<?php

session_start(); 
//destroys the session so the user is no longer signed in.
session_destroy(); 
if(!isset($_SESSION['username']))
{
	header("Location: login.php");
}
$msg = '<p style="text-align: center;">You are now logged out</p>';
include "template.php"; 
$template = new template();
$template->createPage("Logged Out");
?>
<?php print $msg; ?>
<p style="text-align: center;">
<a href="<?php print $template->cwd(); ?>">Click here</a> to return home</p>
<?php $template->endPage(); ?>