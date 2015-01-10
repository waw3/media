<?php
if(!isset($_SESSION['username'])) 
{
	$options="<li onclick=\"javascript:location.href='/media/login.php'\">".
	"Log in</li>".PHP_EOL .
	"<li onclick=\"javascript:location.href='/media/register.php'\">".
	"Register</li></ul>";
}
else
{
	$options="<li onclick=\"javascript:location.href='/media/logout.php'\">".
	"Log out</li>". 
	PHP_EOL . "<li onclick=\"javascript:location.href='/media/movies.php'\">".
	"Movies</li>".PHP_EOL .
	"<li onclick=\"javascript:location.href='/media/shows.php'\">".
	"Shows</li>".PHP_EOL;
	if($_SESSION['group'] == "admin")
	{
		$options = $options .
		"<li onclick=\"javascript:location.href='/media/update.php'\">".
		"Update</li>". PHP_EOL .
		"<li ".$this->getClass().
		"onclick=\"javascript:location.href='/media/admin.php'\">".
		"Admin CP</li></ul>" . PHP_EOL;
	}
	else
	{
		$options = $options .
		"<li onclick=\"javascript:location.href='/media/user.php'\">".
		"User CP</li></ul>". PHP_EOL;
	}
}
		
?>

<div id = "header">
	<div id="nav">
		<ul><li onclick="javascript:location.href=
		'<?php print $this->cwd();?>'">Home</li>
		<?php print $options; ?>
	</div>
</div>