<?php 
require "vendor/autoload.php";
$Core = new Core();
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
<link href="css/signin.css" rel="stylesheet">
    <div class="container" id="login">
      <form class="form-signin" action="login.php" method="post">
        <h2 class="form-signin-heading">Sign in</h2>
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-primary pull-right" type="submit">Sign in</button>
      </form>
    </div> <!-- /container -->
	<?php 
	if(isset($errmsg))
	{
	?>
		<div class="alert alert-danger" role="alert" style="width: 300px; margin: 0 auto;">
			<span class="glyphicon glyphicon-exclamation-sign"></span>
			<span class="sr-only">Error:</span>
			<?php print $errmsg; ?>
		</div>
	<?php
	} $Core->endPage(); ?>