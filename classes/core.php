<?php
date_default_timezone_set('America/Detroit');
require "vendor/autoload.php";
class core
{
	private $cssClass="";
	//gets the current working current directory without the document root.
	public function cwd()
	{
		return substr(getcwd(), strlen($_SERVER['DOCUMENT_ROOT']));
	}
	// connects to database using the config file.
	public function dbConnect() 
	{
		$dbUser=file_get_contents("config/databaseUser.txt");
		if($dbUser === FALSE) { header("Location: setup.php"); }
		$dbUser=explode("\n",$dbUser);

		try {
		$con = new PDO("mysql:host=localhost;dbname=$dbUser[2]",
		"$dbUser[0]","$dbUser[1]");
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo $e->getMessage();
			exit();
		}
		return $con;
	}
	public function requireSSL()
	{
		if($_SERVER['SERVER_PORT'] != '443' && $this->configInfo("ssl") == "on")
		{
			header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit();
		}
	}
	public function setup($dbUser, $dbPass)
	{
		if(!is_dir(getcwd().'/Logs'))
		{
			$file = getcwd()."/Logs";
			if(!mkdir($file,0774,true))
			{
				return "<h2>Unable to create file Logs, please check file permissions</h2>";
			}
		}
		if(!is_dir(getcwd().'/config'))
		{
			$file = getcwd()."/config";
			if(!mkdir($file,0774))
			{
				return "<h2>Unable to create config, please check file permissions</h2>";
			}
		}
		if(!is_dir(getcwd().'/metadata'))
		{
			$file = getcwd()."/metadata/movies";
			if(!mkdir($file,0774,true))
			{
				return "<h2>Unable to create metadata/movies, please check file permissions</h2>";
			}
			$file = getcwd()."/metadata/shows";
			if(!mkdir($file,0774,true))
			{
				return "<h2>Unable to create metadata/shows, please check file permissions</h2>";
			}
		}
		
		try 
		{
			$con = new PDO("mysql:host=localhost;","$dbUser","$dbPass");
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$query = $con->query("show databases");
			$data = $query->fetchAll();
			$dbExists = false;
			for($i = 0; $i < count($data); $i++)
			{
				if($data[$i]['Database'] == "simpleMediaStreamer")
				{
					$dbExists = true;
				}
			}
			if($dbExists)
			{
				$password = substr(password_hash($this->get_random_string(12),
				PASSWORD_DEFAULT),25);
				$con->query("GRANT ALL PRIVILEGES ON simpleMediaStreamer.* TO ".
				"'smsDatabase'@'localhost' IDENTIFIED BY '$password' WITH GRANT OPTION;");
				$f = fopen("config/databaseUser.txt", 'w');
				fwrite($f,"smsDatabase\n$password\nsimpleMediaStreamer");
				fclose($f);
				return "<h3>Created databaseUser file.<h3>";
			}
			$con->query("CREATE DATABASE simpleMediaStreamer");
			$password = substr(password_hash($this->get_random_string(12),
			PASSWORD_DEFAULT),25);
			$con->query("GRANT ALL PRIVILEGES ON simpleMediaStreamer.* TO ".
			"'smsDatabase'@'localhost' IDENTIFIED BY '$password' WITH GRANT OPTION;");
			$f = fopen("config/databaseUser.txt", 'w');
			fwrite($f,"smsDatabase\n$password\nsimpleMediaStreamer");
			fclose($f);
			$con->query("use simpleMediaStreamer");
			$con->query("CREATE TABLE users (ID INT(32) ".
			"NOT NULL AUTO_INCREMENT, username VARCHAR(64), password ".
			"VARCHAR(255), firstname VARCHAR(30), lastname VARCHAR(30), ".
			"regdate DATETIME, ip VARCHAR(15), userGroup VARCHAR(8), ".
			"activated TINYINT, primary KEY (ID));");
			return "<h3>Done, please register an account.<h3>";
		}
		catch(PDOException $e)
		{
				return $e->getMessage();
		}
		
	}
	public function changePassword($username, $nPass, $cPass, $oPass)
	{
		if($nPass == $cPass)
		{
			if(empty($nPass) || empty($cPass) || empty($oPass))
			{ 
			return "<h2>Password can't be empty</h2>"; }
			if(strlen($nPass) < 6)
			{ 
				return "<h2>Password has to be at least 6 characters</h2>"; 
			}
			$sqlQuery = new sql("users", $this->dbConnect());
			$row = $sqlQuery->select("password","username",$username);

			if(password_verify($oPass,$row[0]['password']))
			{
				if(password_verify($nPass,$row[0]['password']))
				{
					return "<h2>That's your current password.</h2>";
				}
				$nPass = password_hash($nPass, PASSWORD_DEFAULT);
				$query = $sqlQuery->update("password","username","$nPass $username");
			}
			else { $con = null; return "<h2>Old password invalid.</h2>"; }
		}
		else { return "<h2>Passwords do not match.</h2>"; }
		return "<h3>Password was successfully changed.</h3>";
	}
	public function login($suppliedUser, $suppliedPass)
	{
			$sqlQuery = new sql("users", $this->dbConnect());
			//sql query to get the information from the database
			$row = $sqlQuery->select("id username password userGroup activated",
			"username",$suppliedUser);
			//checking if the password matches.
			if(password_verify($suppliedPass,$row[0]['password'])) 
			{
			if($row[0]['activated'] == 0)
			{
				return "<h2>Account isn't active.</h2>";
			}
			elseif($row[0]['activated'] == 2)
			{
				return "<h2>Account has been banned.</h2>";
			}
				session_start();
				$root=$this->cwd();
				//setting session variables.
				$_SESSION['username']=$row[0]['username'];
				$_SESSION['activity']=time();
				$_SESSION['group']=$row[0]['userGroup'];
				header("Location: $root ");
			} 
			else
			{
				$f = fopen("Logs/access.log",'a');
				fwrite($f,date('d M Y H:i:s').": User tried to login with username: ".
				" $suppliedUser and failed. Ip address: ".$_SERVER['REMOTE_ADDR']."\n");
				fclose($f);
				 // message that the user didn't have valid information.
				return "<h2>Invalid username or password.".
				"<br/>Please try again.</h2>";
	
			}
	}
	//checks the user agent to see what operating system they are using.
	public function isMobile() 
	{
		if (stripos($_SERVER['HTTP_USER_AGENT'], "android") !== false)
		{ 
			return true; 
		}
		return false;
	}
	public function styles()
	{
		if ($this->isMobile())
		{ 
		?>
			<link href="css/mobilestyle.php" 
			      rel="stylesheet" type="text/css" />	
		<?php
		}
		else
		{
		?>
			<link href="css/style.css" 
			      rel="stylesheet" type="text/css" />
		<?php
		}
	}
	//checks the database for the user's information
	public function register($fName, $lName, $user, $pass, $cPass, $ip)
	{
		$sqlQuery = new sql("users");
		$user=strtolower($user);
		if(preg_match('/\s/',$fName) 
		|| preg_match('/\s/',$lName) 
		|| preg_match('/\s/',$user) 
		|| preg_match('/\s/',$pass) 
		|| preg_match('/\s/',$cPass)){return "<h2>No spaces allowed.</h2>";}
		
		//All of these if statements just check the registration info.
		if($pass != $cPass) { return "<h2>Passwords do not match</h2>"; }
				
		if(strlen($fName) > 30 || strlen($lName) > 30)
		{
			return "<h2>Names cannot be longer than 30 characters.</h2>";
		}
		else if(strlen($fName) < 3 || strlen($lName) < 3)
		{
			return "<h2>Names cannot be less than 3 characters.</h2>";
		}
		elseif(strlen($user) > 20)
		{
			return "<h2>Usernames cannot be ".
			"longer than 20 characters.</h2>";
		}
		elseif(strlen($user) < 3)
		{
			return "<h2>Usernames cannot be ".
			"shorter than 3 characters.</h2>";
		}
		elseif(strlen($pass)>55)
		{
			return "<h2>Password cannot be ".
			"longer than 55 characters.</h2>";
		}
		elseif(strlen($pass)<6)
		{
			return "<h2>Password cannot be ".
			"shorter than 6 characters.</h2>";
		}

		if(strtolower($user) == "public")
		{
			return "<h2>Invalid User</h2>";
		}
		$row = $sqlQuery->select();
		if(count($row) == 0)
		{
			$pass=password_hash($pass,PASSWORD_DEFAULT);
			$sqlQuery->insert("username password firstname lastname".
			" regdate ip userGroup activated",
			"$user $pass $fName $lName now() $ip admin 1");
			return "<h3>Account created.<h3>";
		}
		$row = $sqlQuery->select("username","username","$user");
		if(!empty($row[0]))
		{
			$con = null;
			return "<h2>Username has been taken</h2>";
		}
		$row = $sqlQuery->select("ip regdate","ip","$ip","ORDER BY id DESC");
		$count = count($row);
		if($count > 5)
		{
			$row = $query->fetch();
			$date = $row['regdate'];
			$seconds = strtotime("$date");
			$currentSeconds = strtotime("now");

			if($seconds+86400 > $currentSeconds)
			{
				return "<h2>You have registered too many times please try again in: ".
				round((($seconds+86400)-$currentSeconds)/3600,2) . " hour(s).";
			}
		}
		$pass=password_hash($pass,PASSWORD_DEFAULT);
		$sqlQuery->insert("username password firstname lastname".
		" regdate ip userGroup activated",
		"$user $pass $fName $lName now() $ip standard 0");
		return "<h3>Your account has been created. ".
		"However, the administrator has to activate ".
		"the account.</h3>";
	}
	// use if site should be restricted to registered users.
	public function startSessionRestricted() 
	{
		session_start();
		$dir=$this->cwd();
		$difference=time() - $_SESSION['activity'];
		if(!isset($_SESSION['username']))
		{
			header("Location: $dir/login.php");
		}
		else if($difference > 86400)
		{
			unset($_SESSION);
			session_destroy();
			header("Location: $dir/login.php");
		}
		else
		{
			$con = $this->dbConnect();
			$sqlQuery = new sql("users",$con);
			//This runs to check if the user has been banned.
			$username=$_SESSION['username'];

			$row = $sqlQuery->select("activated","username","$username");
			if($row[0]['activated'] == 2)
			{
				unset($_SESSION);
				session_destroy();
				header("Location: $dir/login.php");
			}
			$_SESSION['activity']=time();
		}
		if($_SESSION['group'] == "admin")
		{
			//This statement will check if there are any users to be activated.
			$con = $this->dbConnect();
			$sqlQuery = new sql("users",$con);
			$row = $sqlQuery->select("activated","activated","0");
			if(count($row) != 0)
			{
				$this->setClass('class="newUser"');
			}
		}
}
	public function getClass()
	{
		return $this->cssClass;
	}
	public function setClass($var)
	{
		$this->cssClass=$var;
	}
	// use if page should be restricted to admin group.
	public function startSessionAdmin() 
	{
		$this->startSessionRestricted();
		if(!($_SESSION['group'] == "admin")) 
		{
			$f = fopen("Logs/access.log",'a');
			fwrite($f,date('d M Y H:i:s').": ".$_SESSION['username']." tried to access restricted page".
			" Ip address: ".$_SERVER['REMOTE_ADDR']."\n");
			fclose($f);
			$dir=$this->cwd();
			header("Location: $dir");
		}
	}
	public function searchBar($action)
	{
	?>
		<div id="searchbar">
			<form action ="<?php print $action; ?>" method="post">
				<input type="search" id="textfield" style="height:24px" name="searchtext"/>
				<input id="submit" type="submit" value="Search"
				name="search" style="font-size: 16px; margin-left: 5px;" />
			</form>
		</div>
	<?php
	}
	public function header()
	{
		if(!isset($_SESSION['username'])) 
		{
			$options="<li onclick=\"javascript:location.href='login.php'\">".
			"Log in</li>".PHP_EOL .
			"<li onclick=\"javascript:location.href='register.php'\">".
			"Register</li></ul>";
		}
		else
		{
			$options="<li onclick=\"javascript:location.href='logout.php'\">".
			"Log out</li>". 
			PHP_EOL . "<li onclick=\"javascript:location.href='movies.php'\">".
			"Movies</li>".PHP_EOL .
			"<li onclick=\"javascript:location.href='shows.php'\">".
			"Shows</li>".PHP_EOL;
			if($_SESSION['group'] == "admin")
			{
				$options = $options .
				"<li onclick=\"javascript:location.href='update.php'\">".
				"Update</li>". PHP_EOL .
				"<li ".$this->getClass().
				"onclick=\"javascript:location.href='admin.php?edit=list'\">".
				"Admin CP</li></ul>" . PHP_EOL;
			}
			else
			{
				$options = $options .
				"<li onclick=\"javascript:location.href='user.php'\">".
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
<?php
	}
	//creates opening tags and accepts a page title..
	public function createPage($title, $function="",$var="") 
	{	
	?>
		<!doctype html>
		<html>
		<head>
			<link rel="shortcut icon" href="/media/favicon.ico">
			<title><?php echo $title; ?></title>
			<meta name="viewport" content="minimal-ui, width=device-width,
						initial-scale=1, maximum-scale=1, user-scalable=no">
			<meta http-equiv="Cache-control" content="public">
			<meta charset="UTF-8">
			<?php $this->styles(); ?>
			<script src="javascript/functions.js"></script>			
		</head>	
		<body>
			<div id ="wrapper">
			<?php if(!$this->isMobile()){ $this->header(); } ?>
			<?php if(!empty($function)){$this->$function($var);}?>
			<div id="main" >
	<?php
	}
	//echos the closing tags of the html page.
	public function endPage($function = "") 
	{
	?>
		</div>
		<?php if($this->isMobile()){ $this->header(); } ?>
		</div>
		<?php if(!empty($function)){$this->$function(); } ?>
		</body>
		</html>
	<?php
	}
	function lazyLoad()
	{
	?>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="javascript/jquery.lazyload.js"></script>
		<script type="text/javascript" charset="utf-8">
		$(function() {
			$("img.lazy").lazyload({  
			effect : "fadeIn", container: $("#main")
			});
		});
		</script>
	<?php
	}
	//returns the difference of the current date to the date provided in hours.
	function compareDate($dateToCompare) 
	{
		date_default_timezone_set('America/Detroit');
		$currentDate=(strtotime(date('Y-m-d H:i:s')));
		$otherDate=(strtotime($dateToCompare));
		$difference=($currentDate - $otherDate)/3600;
		return round($difference,2);
	}
	// converts seconds into hours minutes seconds.
	function secondsToTime($seconds) 
	{
		$H=floor($seconds / 3600);
		$i=($seconds / 60) % 60;
		$s=$seconds % 60;
		return sechof("%02d:%02d:%02d", $H, $i, $s);
	}
	function videojsScripts()
	{
	?>
		<link href="//vjs.zencdn.net/4.5/video-js.css" rel="stylesheet">
		<script src="//vjs.zencdn.net/4.5/video.js"></script>
		<style type="text/css">
		.vjs-default-skin .vjs-play-progress,
		.vjs-default-skin .vjs-volume-level { background-color: #ff0000 }
		.vjs-default-skin .vjs-big-play-button { background: rgba(0,0,0,1) }
		.vjs-default-skin .vjs-slider background: rgba(0,0,0,0.3333333333333333) }
		</style>
	<?php
	}
	function videojs($videoname, $width, $height, $length = "",$time="")
	{
		$video = explode("?media=",$videoname);
		$video = urldecode($video[1]);
		if(strpos($video,"&time="))
		{
			$video = explode("&time=",$video);
			$video = $video[0];
		}
		if(strpos($video,"&br="))
		{
			$video = explode("&br=",$video);
			$video = $video[0];
		}
	
		?>
		<video id="MY_VIDEO_1"
		class="video-js vjs-default-skin vjs-big-play-centered" controls
		width="<?php print $width; ?>" 
		height="<?php print $height; ?>">
		<?php
		if(!empty($length))
		{
		
		?>
		<script type="text/javascript">
			var video= videojs('MY_VIDEO_1');
			var cbitrate = false;
			video.src("<?php print $videoname; ?>");
			video.load();
			setTimeout(function(){
			video.play();
			}, 2000);
			// hack duration
			video.duration= function() { return video.theDuration; };
			<?php if(!empty($time))
			{
				
			?>
			video.start = <?php print $time; ?>;
			<?php
			$time = "";
			}
			else
			{
			?>
			video.start= 0;
			<?php
			}
			?>
			video.oldCurrentTime= video.currentTime;
			video.currentTime= function(time) 
			{ 
				if( time == undefined )
				{
					return video.oldCurrentTime() + video.start;
				}
				video.oldCurrentTime(0);
				video.start = time;
				$.get( "time.php", { media: "<?php print $video;?>", 
				time: Math.trunc(time) } );
				playvideo("<?php print $videoname."&time="; ?>" + Math.trunc(time));
				video.load()
				//since the transcoding is real time a little buffer is nice.
				setTimeout(function(){
				video.play();
				}, 2000);
				return this;
			};
			video.theDuration=<?php print $length; ?>;
			setInterval(function() {
			$.get( "time.php", { media: "<?php print $video;?>",
			time: Math.trunc(video.currentTime()) } );
			}, 30000);
		</script>
		</video>
		<?php
		}
		else
		{
		?>
		<script>
			var video= videojs('MY_VIDEO_1');
			video.src("<?php print $videoname; ?>");
			video.currentTime(0);
		</script>
		</video>
		<?php
		}
	}
	function get_random_string($length)
	{
		$random_string="";
		$valid_chars="abcdefghijklmnopqrstuvwxyz1234567890";
		$num_valid_chars=strlen($valid_chars);
		for ($i=0; $i < $length; $i++)
		{
			$random_pick=mt_rand(1, $num_valid_chars);
			$random_char=$valid_chars[$random_pick-1];
			$random_string .= $random_char;
		}
		return $random_string;
	}
	function getBrowser() 
	{ 
		$u_agent=$_SERVER['HTTP_USER_AGENT'];   
		if(preg_match('/Trident/i',$u_agent))
		{
			return "MSIE"; 
		}
		elseif(preg_match('/Firefox/i',$u_agent)) { return "Firefox"; }
		elseif(preg_match('/Chrome/i',$u_agent)) { return "Chrome"; }
		elseif(preg_match('/Safari/i',$u_agent)) { return "Safari"; }
		elseif(preg_match('/Opera/i',$u_agent)) { return "Opera"; }
		elseif(preg_match('/Netscape/i',$u_agent)) { return "Netscape"; }
	}
	function header2()
	{
	?>
		<div id = "header2">
		<ul><li onclick="javascript:location.href='music.php?v=pub'">Public</li>
		<li onclick="javascript:location.href='music.php?v=priv'">
		Private</li><li onclick="javascript:location.href='music.php?m=upload'">
		Upload</li></ul>
		</div>
<?php
	}
	function adminMenu()
	{
		?>
		<div id = "header2">
		<ul><li onclick="javascript:location.href='admin.php?edit=list'">Users</li>
		<li onclick="javascript:location.href='admin.php?edit=settings'">
		Settings</li><li onclick="javascript:location.href='setup.php'">
		Setup</li></ul>
		</div>
<?php
	}
	function clean($var, $dir = "")
	{
		if(!empty($dir))
		{
			$tmpArray = preg_split("/[\s.]+/",$var);
			$tmpArray = array_filter($tmpArray, create_function('$var',
			'return !(preg_match("/(?:mp4|avi|mkv)|'.
			'(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)'.
			'|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|nSD|WEB|1-PSY|XviD-LOL|REPACK|DL|(?:AC\d)/i", $var));'));
			return implode(" ",$tmpArray);
		}
		$tmpArray = preg_split("/[\s.]+/",$var);
		$tmpArray = array_filter($tmpArray, create_function('$var',
		'return !(preg_match("/(?:mp4|avi|mkv)|'.
		'(?:HDTV|bluray|WEB-DL|IMAX|EDITION|DTS|DrunkinRG|\w{2,3}rip)'.
		'|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|nSD|WEB|1-PSY|XviD-LOL|REPACK|DL|(?:AC\d)/i", $var));'));
		return implode(" ",$tmpArray);
	}
	function configInfo($val="")
	{
		$config = @file_get_contents("config/config.json");
		if($config === false){return null;}
		if(empty($val)){ return json_decode($config, true);}
		$config = json_decode($config, true);
		if($val == "ssl"){return $config['ssl'];}
		if($val == "movieDir"){return $config['movieDir'];}
		if($val == "showDir"){return $config['showDir'];}
		if($val == "musicDir"){return $config['musicDir'];}
		if($val == "bitrate"){return $config['bitrate'];}
		
	}
	function movieInfo($dir, $val="")
	{
		$dir = escapeshellarg($dir);
		$cmd = "/usr/local/bin/ffprobe -v quiet -print_format json -show_format -show_streams $dir";
		$array = json_decode(shell_exec($cmd),true);
		if(empty($val)){return $array;}
		if($val == "vBitrate")
		{
			if(!empty($array[streams][0]['bit_rate']))
			{
				return round($array[streams][0]['bit_rate']/1024,0);
			}
			else
			{
				if(!empty($array[streams][1]['bit_rate']))
				{
					return round(($array[format]['bit_rate']-$array[streams][1]['bit_rate'])/1024,0);
				}
				else if(!empty($array[format]['bit_rate']))
				{
					return round($array[format]['bit_rate']/1024,0);
				}
				else{return $this->configInfo("bitrate");}
			}
		}
		if($val == "aBitrate"){return round($array[streams][1]['bit_rate']/1024,0);}
		if($val == "vCodec"){return $array[streams][0]['codec_name'];}
		if($val == "aCodec"){return $array[streams][1]['codec_name'];}
		if($val == "length")
		{
			return $array[format]['duration'];
		}
		if($val == "size")
		{
			$cmd = "ls -l  $dir | awk '{print $5}'";
			return shell_exec($cmd);
		}
	}
	function playVideo($dir,$time="",$bitrate="")
	{
	
		if(strpos($dir,"shows") === false)
		{
			$dir = "movies/" . html_entity_decode($dir);
		}
		if(!empty($time) && !empty($bitrate))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&time=".$time."&br=".$bitrate;
		}
		else if(!empty($time))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&time=".$time;
		}
		else if (!empty($bitrate))
		{
			$urldir = "transcode.php?media=".urlencode($dir)."&br=".$bitrate;
		}
		else
		{
			$urldir = "transcode.php?media=".urlencode($dir);
		}
		$length = $this->movieInfo($dir,"length");
		$this->videojsScripts();
		$br = $this->configInfo("bitrate");
		$mBr = $this->movieInfo($dir,"vBitrate");
		if(!empty($br) && $br <= $mBr){$maxBR = $br;}
		else{$maxBR = $mBr;}
		?>
		<select class="list1" onchange="changebr(<?php print $this->movieInfo($dir,"length");?>)">
		<?php
		for($i = 256; $i <= $maxBR; $i=$i*2)
		{
			if($i < $maxBR)
			{
			?>
			<option value="<?php print $i;?>"><?php print $i;?> kbps</option>
			<?php
			}
			if($i*2 > $maxBR || $i == $maxBR)
			{
			?>
				<option value="<?php print $maxBR;?>" selected><?php print $maxBR;?> kbps</option>
			<?php
			}	
		}
		?>
		</select>
		<br>
		<?php
		$this->videojs($urldir, 640, 360, $length,$time);
	}
}
?>