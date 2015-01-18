<?php

session_start(); 
//destroys the session so the user is no longer signed in.
session_destroy(); 
if(!isset($_SESSION['username']))
{
	header("Location: login.php");
}
$msg = '<p style="text-align: center; margin-top: 20%;">You are now logged out</p>';
require "vendor/autoload.php";
$core = new core();
$core->createPage("Logged Out");
?>
<?php print $msg; ?>
<p style="text-align: center;">
<a href="<?php print $core->cwd(); ?>">Click here</a> to return home</p>
<?php $core->endPage(); ?>