<?php
		if(!isset($_SESSION['username'])) 
		{
			$inOut = "<li onclick=\"javascript:location.href='/media/login.php'\">Log in</li>"  . PHP_EOL . "<li onclick=\"javascript:location.href='/media/register.php'\">Register</li></ul>";
		}
		elseif($_SESSION['group'] == "admin")
		{
			$inOut = "<li onclick=\"javascript:location.href='/media/logout.php'\">Log out</li>"  . PHP_EOL . "<li onclick=\"javascript:location.href='/media/movies.php'\">Movies</li>" . PHP_EOL ."<li onclick=\"javascript:location.href='/media/update.php'\">Update</li>".PHP_EOL ."<li ".$this->getClass()."onclick=\"javascript:location.href='/media/admin.php'\">Admin CP</li></ul>".PHP_EOL;
		}
		else
		{
			$inOut = "<li onclick=\"javascript:location.href='/media/logout.php'\">Log out</li>"  . PHP_EOL . "<li onclick=\"javascript:location.href='/media/movies.php'\">Movies</li>" . PHP_EOL;
		}
?>

		<div id = "header">
		<div id="nav">
		<ul><li onclick="javascript:location.href='<?php print $this->cwd();?>'">Home</li>
		<?php print $inOut ; ?>
		</div>
		</div>