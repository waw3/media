<?php 
session_start();
require "vendor/autoload.php";
$core = new core();
$core->createPage("Simple Media Streamer");
if(file_exists("config/databaseUser.txt"))
{
	print "<h2>Setup has already ran, to rerun please delete the databaseUser file.</h2>".PHP_EOL;
	$core->endPage();
	exit();
}
if(!empty($_POST['username']))
{
	if(empty($_POST['password']))
	{
		echo "<h2>Database root user needs password.</h2>".PHP_EOL;
	}
	echo $core->setup($_POST['username'],$_POST['password']);
}
?>
<h1>Setup Database</h1>
<form id="centerform" action="setup.php" method="post" enctype="multipart/form-data" style="width: 350px;">
	<p>MySQL root: <input type="text" name="username" required/></p>
	<p>MySQL password: <input type="password" name="password" required/></p>
	<input id="button" type="submit" value="Setup" name="submit" /><br>
</form>
<?php $core->endPage(); ?>