<?php
require "vendor/autoload.php"; 
$Core = new Core();
$Core->createPage("Register", false);
$msg = "";
if($_POST['username'])
{
	$msg = $Core->register($_POST["fName"], $_POST["lName"],
	$_POST["username"], $_POST["password"], $_POST["cPassword"],
	$_SERVER['REMOTE_ADDR']);
	if(isset($msg) && strpos($msg,"Success") === false)
	{
		$fname = $_POST["fName"];
		$lname = $_POST["lName"];
		$username = $_POST["lName"];
	}
}
?>
<link href="css/signup.css" rel="stylesheet">
	<div class="container" id="register">
      <form class="form-signup" action="" method="post">
        <h2 class="form-signup-heading">Register</h2>
		<label for="inputFirstName" class="sr-only">First Name</label>
		<input type="text" id="fName" name="fName" class="form-control" value="<?php print $fname;?>" placeholder="First Name" required autofocus>
		<label for="inputFirstName" class="sr-only">Last Name</label>
		<input type="text" id="lName" name="lName" class="form-control" value="<?php print $lname;?>" placeholder="Last Name" required autofocus>
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="username" name="username" class="form-control" value="<?php print $username;?>" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
		<label for="inputPassword" class="sr-only">Repeat Password</label>
        <input type="password" id="password" name="cPassword" class="form-control" placeholder="Repeat Password" required>
        <button class="btn btn-primary pull-right btn-margin" style="margin-top: 10px;" type="submit">Register</button>
      </form>
    </div> <!-- /container -->
	<?php
	if(isset($msg) && !empty($msg))
	{
		if(strpos($msg,"activate") === false)
		{
		?>
			<div class="alert alert-danger" role="alert" style="width: 300px; margin: 0 auto;">
				<span class="glyphicon glyphicon-exclamation-sign"></span>
				<span class="sr-only">Error:</span>
				<?php print $msg; ?>
			</div>
		<?php
		}
		else
		{
		?>
			<div class="alert alert-success" role="alert" style="width: 300px; margin: 0 auto;">
				<span class="glyphicon glyphicon-ok"></span>
				<?php print $msg; ?>
			</div>
		<?php
		}
	}
$Core->endPage(); ?>
