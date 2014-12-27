<?php
class template
{
	private $cssClass="";
	//gets the current working current directory without the document root.
	public function cwd() 
	{
		return substr(getcwd(), strlen($_SERVER['DOCUMENT_ROOT']));
	}
	public function dbConnect() // connects to database using the config file.
	{
		$dbUser=file_get_contents("config/databaseUser.txt");
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
	public function changePassword($username, $nPass, $cPass, $oPass)
	{
		$con=$this->dbConnect();
		if($nPass == $cPass)
		{
			if(empty($nPass) || empty($cPass) || empty($oPass))
			{ 
			return "<h2>Password can't be empty</h2>"; }
			if(strlen($nPass) < 6)
			{ 
				return "<h2>Password has to be at least 6 characters</h2>"; 
			}
			$sql = "SELECT password from users WHERE username = ?";
			$query = $con->prepare($sql);
			$query->execute(array($username));
			$row = $query->fetch(PDO::FETCH_ASSOC);

			if(password_verify($oPass,$row['password']))
			{
				if(password_verify($nPass,$row['password']))
				{
					return "<h2>That's your current password.</h2>";
				}
				$nPass = password_hash($nPass, PASSWORD_DEFAULT);
				$sql = "UPDATE users SET password = ? WHERE username = ?";
				$query = $con->prepare($sql);
				$query->execute(array($nPass, $username));
				$con = null;
			}
			else { $con = null; return "<h2>Old password invalid.</h2>"; }
		}
		else { return "<h2>Passwords do not match.</h2>"; }
		mysqli_close($con);
		return "<h3>Password was successfully changed.</h3>";
	}
	public function login($suppliedUser, $suppliedPass)
	{
			$con=$this->dbConnect();
			//sql query to get the information from the database
			$sql="SELECT id, username, password, userGroup,". 
			" activated FROM users WHERE username = ? LIMIT 1;"; 
			$query = $con->prepare($sql);
			$query->execute(array($suppliedUser));
			$row = $query->fetch(PDO::FETCH_ASSOC);

			if($row['activated'] == 0){
				return "<h2>Account isn't active.</h2>";
			}
			elseif($row['activated'] == 2)
			{
				return "<h2>Account has been banned.</h2>";
			}
			//checking if the password matches.
			if(password_verify($suppliedPass,$row['password'])) 
			{
				session_start();
				$root=$this->cwd();
				//setting session variables.
				$_SESSION['username']=$row['username'];
				$_SESSION['activity']=time();
				$_SESSION['group']=$row['userGroup'];
				header("Location: $root ");
			} 
			else
			{
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
			<link href="/media/css/mobilestyle.php" 
			      rel="stylesheet" type="text/css" />	
		<?php
		}
		else
		{
		?>
			<link href="/media/css/style.php" 
			      rel="stylesheet" type="text/css" />
		<?php
		}
	}
	//checks the database for the user's information
	public function register($fName, $lName, $user, $pass, $cPass, $ip)
	{
		$con=$this->dbConnect();
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

		$sql="SELECT username FROM users WHERE username= ?;";
		$query = $con->prepare($sql);
		$query->execute(array($user));
		$row=$query->fetch();
		if(!empty($row))
		{
			$con = null;
			return "<h2>Username has been taken</h2>";
		}
		$pass=password_hash($pass,PASSWORD_DEFAULT);
		$sql = "INSERT INTO users ".
		"(username,password,firstname,lastname, ".
		"regdate, ip, userGroup, activated) VALUES ".
		"(?, ?, ?, ?, now(), ".
		"?,'standard', '0')";
		$query = $con->prepare($sql);
		$query->execute(array($user, $pass, $fName, $lName, $ip));
		$con = null;
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
			//This runs to check if the user has been banned.
			$username=$_SESSION['username'];
			$con=$this->dbConnect();
			//sql query to get the information from the database
			$sql="SELECT activated FROM users WHERE ". 
			"username='$username' LIMIT 1;"; 
			$query=$con->query($sql);
			$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row['activated'] == 2)
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
			$con=$this->dbConnect();
			$sql="SELECT activated FROM users WHERE activated='0';";
			$query=$con->query($sql);
			$row=$query->fetch(PDO::FETCH_ASSOC);
			if($query->rowCount() != 0)
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
			$dir=$this->cwd();
			header("Location: $dir");
		}
	}
	public function customScrollLazyLoad()
	{
		if (!$this->isMobile())
		{ 
		?>
		
		<?php
		}
	}
	//creates opening tags and accepts a page title..
	public function createPage($title, $function="") 
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
			<?php if(!empty($function)){$this->$function(); $this->loadBar();}?>
		</head>	
		<body>
		<?php $this->styles(); 
		?>
			<div id ="wrapper">
				<div id="searchbar">
				<form action ="movies.php" method="post" 
				style="margin-top: 5px;">
					<input id="submit" type="submit" value="Search" 
					name="search" style="height: 17px;" />
					<input type="search" id="textfield" name="searchtext"/>
				</form>
				</div>
			<?php if(!$this->isMobile()){ include "header.php"; } ?>
			<div id="main" >
	<?php
	}
	//echos the closing tags of the html page.
	public function endPage() 
	{
	?>
		</div>
		<?php if($this->isMobile()){ include "header.php"; } ?>
		</div>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="javascript/jquery.lazyload.js"></script>
		<script type="text/javascript" charset="utf-8">
		$(function() {
			$("img.lazy").lazyload({  
			effect : "fadeIn", container: $("#main")
			});
		});
		</script>
		</body>
		</html>
	<?php
	}
	function loadBar()
	{
		?>
		<script src="javascript/pace.js"></script>
		<link href="css/pace-theme-minimal.css" rel="stylesheet" />
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
	function videojs($videoname, $width, $height, $type, $length="")
	{
		if($this->isMobile())
		{
			//$width=$width/2;
			//$height=$height/2;
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
		<script>
		var video= videojs('MY_VIDEO_1');
				
		video.src("<?php print $videoname."&quality=High&time=0"; ?>");
		// hack duration

		video.duration= function() { return video.theDuration; };
		
		video.start= 0;
		video.oldCurrentTime= video.currentTime;
		video.currentTime= function(time) 
		{ 
			if( time == undefined )
			{
				return video.oldCurrentTime() + video.start;
			}
			console.log(time)
			video.start= time;
			video.oldCurrentTime(0);
			video.src("<?php print $videoname."&quality=High&time=" ?>" 
			+ Math.trunc(time));
			video.play();
			return this;
		};
			video.theDuration= <?php print $length; ?>;
	</script>
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
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			return "MSIE"; 
		}
		elseif(preg_match('/Firefox/i',$u_agent)) { return "Firefox"; }
		elseif(preg_match('/Chrome/i',$u_agent)) { return "Chrome"; }
		elseif(preg_match('/Safari/i',$u_agent)) { return "Safari"; }
		elseif(preg_match('/Opera/i',$u_agent)) { return "Opera"; }
		elseif(preg_match('/Netscape/i',$u_agent)) { return "Netscape"; }
	}
}
?>