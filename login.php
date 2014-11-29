<?php 
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
include "template.php"; 
$template = new template();
$con = $template->dbConnect();
if(isset($_POST['username'])) // checking if that variable is set, when the form below is filled out this will be true.
{
	$suppliedUser = mysqli_real_escape_string($con, strip_tags($_POST["username"])); //setting username supplied from the user
	$suppliedPass = mysqli_real_escape_string($con, strip_tags($_POST["password"])); //setting password supplied from the user
	$suppliedUser = strtolower($suppliedUser); //setting username to be all lowercase.
	$errmsg = $template->login($suppliedUser,$suppliedPass);
}
$template->createPage("Login");
?>
	<h1>Login</h1>
	<center>
	<form id="login" action="login.php" method="post" enctype="multipart/form-data" style="width: 300px;">
		<p>Username: <input type="text" name="username" required/></p>
		<p>Password: <input type="password" name="password" required/></p>
		<input id="button" type="submit" value="Login" name="submit" />
		<?php print $errmsg; ?>
	</form>
	</center>
<?php $template->endPage(); ?>