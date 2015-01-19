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
$Core = new Core();
$Core->createPage("Logged Out");
?>
<?php print $msg; ?>
<p style="text-align: center;">
<a href="<?php print $Core->cwd(); ?>">Click here</a> to return home</p>
<?php $Core->endPage(); ?>