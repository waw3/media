<?php
include "template.php"; 
$template = new template();
$template->startSessionAdmin();
$files = glob("metadata/movies/*txt");
$movies = glob("movies/*.{mp4,mkv,avi}",GLOB_BRACE );
$count = shell_exec('pgrep -f lMovieMeta | wc -l');
if($count < 2)
{
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(0);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(1);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(2);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(3);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(4);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(5);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(6);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(7);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(8);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(9);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(10);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(11);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(12);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(13);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(14);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lMovieMeta.php\'; metadata(15);" > /dev/null &');
	
}
$count2 = shell_exec('pgrep -f lShowMeta | wc -l');
if($count < 2)
{
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(0);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(1);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(2);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(3);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(4);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(5);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(6);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(7);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(8);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(9);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(10);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(11);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(12);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(13);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(14);" > /dev/null &');
	exec('/usr/local/bin/php '.
	'-r "require \'lShowMeta.php\'; metadata(15);" > /dev/null &');
}
$template->createPage("Simple Media Streamer");
$unmatched = 0;
$matched = 0;
print '<div style="margin-left: 0;width: 500px;">'.PHP_EOL;
print '<div style="float: left; width: 250px; margin-bottom: 25px;">'.PHP_EOL;
$count = 0;
foreach($movies as $value)
	{
		
		$value = basename($value);
		$title = substr($value,0,strlen($value)-4);
		$result = preg_grep("/$title/",$files);
		if(strlen($title) > 25)
		{
			$title = substr($title,0,25) . "...";
		}
		if(count($result) >= 1)
		{
			$matched++;
		}
		else
		{
			print '<p style="color: red; margin-right: 0px; display: inline;".
			" font-size: 12px;">'.$title.'</p><br>'.PHP_EOL;
			$unmatched++;
		}		
	}
	print '</div>'.PHP_EOL;
	print '<div style="float: right; width: 250px; height: 50px;">'.PHP_EOL;
	print '<p style="margin-right: 0px; margin-top: 0px; display: inline; font-size:'.
	' 12px;">Matched Files: '.$matched.'</p><br>'.PHP_EOL;
	print '<p style="color: red; margin-right: 0px; margin-top: 0px; display: inline; '.
	'font-size: 12px;">Unmatched Files: '.$unmatched.'</p><br>'.PHP_EOL;
	print '</div>'.PHP_EOL;
	print '</div>'.PHP_EOL;
	function filter ($var, $search) 
	{ 
		basename($var); 
		return (stripos(strtolower($var), strtolower($search)) !== false); 
	}
$template->endPage(); ?>