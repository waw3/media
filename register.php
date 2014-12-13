<?php
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
require "template.php"; 
$template = new template();
$template->createPage("Register", false);
$msg = "";
if($_POST['username'])
{
	$msg = $template->register($_POST["fName"], $_POST["lName"], $_POST["username"], $_POST["password"], $_POST["cPassword"], $_SERVER['REMOTE_ADDR']);
}
?>
	<h1>Register</h1>
	<center>
	<form action="register.php" method="post" enctype="multipart/form-data" style="width: 375px;">
		<p>First Name: <input  type="text" name="fName" required/></p>
		<p>Last Name: <input type="text" name="lName" required/></p>
		<p>Username: <input type="text" name="username" required/></p>
		<p>Password: <input type="password" name="password" required/></p>
		<p>Confirm Password: <input type="password" name="cPassword" required/></p>
		<input type="submit" id="button" value="Register" name="submit" /><br>
	</form>
	<?php print $msg;?>
	</center>
<?php $template->endPage(); ?>
