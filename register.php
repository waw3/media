<?php
require "vendor/autoload.php";
$Core = new Core();
$Core->requireSSL();
$Core->createPage("Register");
$msg = "";
if($_POST['username'])
{
	$msg = $Core->register($_POST["fName"], $_POST["lName"],
	$_POST["username"], $_POST["password"], $_POST["cPassword"],
	$_SERVER['REMOTE_ADDR']);
}
?>
	

	<form id="centerform" action="register.php" method="post" style="width: 450px; height: 275px; margin-top: 10%;">
	<h1>Register</h1>
		<p><formtext>First Name:</formtext> <input  type="text" name="fName" required/></p>
		<p><formtext>Last Name:</formtext> <input type="text" name="lName" required/></p>
		<p><formtext>Username:</formtext> <input type="text" name="username" required/></p>
		<p><formtext>Password:</formtext> <input type="password" name="password" required/></p>
		<p><formtext>Confirm Password:</formtext> <input type="password" name="cPassword" required/></p>
		<input type="submit" id="button" value="Register" name="submit" /><br>
	</form>
	<?php print $msg;?>
<?php $Core->endPage(); ?>
