<?php 
if($_SERVER['SERVER_PORT'] != '443')
{
	header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit();
}
require "template.php";
$template = new template();
if(isset($_POST['username'])) 
{
	$errmsg = $template->login(strtolower($_POST["username"]),$_POST["password"]);
}
$template->createPage("Login");
?>
	<h1>Login</h1>
	<form id="centerform" action="login.php" method="post" style="width: 300px;">
		<p>Username: <input type="text" name="username" required/></p>
		<p>Password: <input type="password" name="password" required/></p>
		<input id="button" type="submit" value="Login" name="submit" /><br>
	</form>
<?php print $errmsg;
$template->endPage(); ?>