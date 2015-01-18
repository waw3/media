<?php 
require "vendor/autoload.php";
$core = new core();
$core->requireSSL();
if(isset($_POST['username'])) 
{
	$errmsg = $core->login( strtolower($_POST["username"]),$_POST["password"]);
}
$core->createPage("Login");
?>
	<h1 style="margin-top: 10%;">Login</h1>
	<form id="centerform" action="login.php" method="post" style="width: 300px;">
		<p>Username: <input type="text" name="username" required/></p>
		<p>Password: <input type="password" name="password" required/></p>
		<input id="button" type="submit" value="Login" name="submit" /><br>
	</form>
	<?php print $errmsg; ?>
<?php $core->endPage(); ?>