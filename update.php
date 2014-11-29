<?php
include "template.php"; 
$template = new template();
$template->startSessionAdmin();
$count = shell_exec('pgrep -f loadmetadata | wc -l');
if($count < 2)
{
	exec('/usr/local/bin/php loadmetadata.php > /dev/null &');
}
header("Location: movies.php");

?>
