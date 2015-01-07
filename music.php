<?php 
if($_SERVER['SERVER_PORT'] != '443')
{
	header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit();
}
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

require "template.php";
$template = new template();
$template->startSessionRestricted();
$template->createPage("Music");
$template->header2();
if(!empty($_GET['p']))
{
 $path = shell_exec('find /mediastorage/media/Music -name "'.$_GET['p'].'"');
 $path = str_replace("/mediastorage/media/Music","music/public",$path);

?>
	<center>
	<h1><?php print $_GET['p'];?></h1>
	<audio controls autoplay>
	<source src="<?php print $path; ?>" type="audio/mpeg">
	</audio>
	</center>
<?php
}
else if(!empty($_GET['pr']))
{

?>
	<center>
	<h1><?php print $_GET['pr'];?></h1>
	<audio controls autoplay>
	<source src="music/<?php print $_SESSION['username']."/".$_GET['pr'];?>"
	type="audio/mpeg">
	</audio>
	</center>
<?php
}
if($_GET['v'] == "pub")
{
	$files = rglob("/mediastorage/media/Music/*mp3");
	foreach ($files as $value) {
		$value = str_replace("\ ", " ",basename($value));
		$get = urlencode($value);
		$get = "music.php?p=".$get."&v=pub";
		echo "<div id=\"musiclist\" onclick='javascript:location.href=\"$get\"'>".
		PHP_EOL;
		echo "$value";
		echo "</div>".PHP_EOL;
	}
}
else if($_GET['v'] == "priv")
{

	$files = glob("music/".$_SESSION['username']."/*mp3");
	foreach ($files as $value) {
		$value = str_replace("\ ", " ",basename($value));
		$get = urlencode($value);
		$get = "music.php?pr=".$get."&v=priv";
		echo "<div id=\"musiclist\" onclick='javascript:location.href=\"$get\"'>".
		PHP_EOL;
		echo "$value";
		echo "</div>".PHP_EOL;
	}
}
else if($_GET['m'] == "upload")
{
	$cmd = "du -sh ".getcwd()."/music/".$_SESSION['username']." | awk '{print $1}'";
	$test = shell_exec($cmd);
	$test = substr($test,0,strlen($test)-2);
	$value = 100;
	if(!($test > $value))
	{
		echo "<h1>You have used $test MB out of $value MB</h1>";
	?>
	<link href="css/dropzone.css" type="text/css" rel="stylesheet" />
	<script src="javascript/dropzone.min.js"></script>
	<center>
	<form action="upload.php" class="dropzone" style="width: 500px; height: 500px;
	 background-color: #505050;></form>
	</center>
	<?php
	}
	else
	{
		echo "<h1>You have exceeded the upload limit.</h1>";
	}
}
$template->endPage();
?>