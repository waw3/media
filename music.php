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

if($_GET['v'] == "pub")
{
	$files = rglob("/mediastorage/media/Music/*mp3");
	foreach ($files as $value) {
		$value = str_replace("\ ", " ",basename($value));
		$get = urlencode($value);
		$get = "music.php?p=".$get;
		echo "<p style=\"background-color: black; color: green; margin: 0 auto; width: 500px; text-align: left;\"><a href=\"$get\">$value</a></p><br>";
	}
}
else if($_GET['v'] == "priv")
{

	$files = glob("music/".$_SESSION['username']."/*mp3");
	foreach ($files as $value) {
		$value = str_replace("\ ", " ",basename($value));
		$get = urlencode($value);
		$get = "music.php?pr=".$get;
		echo "<p style=\"background-color: black; color: green; margin: 0 auto; width: 500px; text-align: left;\"><a href=\"$get\">$value</a></p><br>";
	}
}
else if($_GET['m'] == "upload")
{
?>
<link href="css/dropzone.css" type="text/css" rel="stylesheet" />
<script src="javascript/dropzone.min.js"></script>
<center>
<form action="upload.php" class="dropzone" style="width: 500px; height: 500px; background-color: #505050;></form>
</center>
<?php
}
else if(!empty($_GET['pr']))
{

?>
	<center>
	<h1><?php print $_GET['pr'];?></h1>
	<audio controls autoplay>
	<source src="music/<?php print $_SESSION['username']."/".$_GET['pr'];?>" type="audio/mpeg">
	</audio>
	</center>
<?php
}
else if(!empty($_GET['p']))
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
$template->endPage();
?>