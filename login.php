<?php 
require "vendor/autoload.php";
$Core = new Core();
$Core->requireSSL();
if(isset($_POST['username'])) 
{
	if(isset($_GET['return']))
	{
		$errmsg = $Core->login( strtolower($_POST["username"]),$_POST["password"],
		urldecode($_GET['return']));
	}
	else
	{
		$errmsg = $Core->login( strtolower($_POST["username"]),$_POST["password"]);
	}
}
$Core->createPage("Login");

?>
	
	<form id="centerform" action="" method="post" style="width: 300px; height: 160px;
	margin-top: 10%;">
		<h1>Login</h1>
		<p>Username: <input type="text" name="username" required/></p>
		<p>Password: <input type="password" name="password" required/></p>
		<input id="button" type="submit" value="Login" name="submit" /><br>
	</form>
	<?php print $errmsg; ?>
<?php $Core->endPage(); ?>