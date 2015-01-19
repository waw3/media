<?php
date_default_timezone_set('America/Detroit');
require "vendor/autoload.php";
class Core
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
		if($_SERVER['SERVER_PORT'] != '443' && Media::configInfo("ssl") == "on")
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
			<script src="javascript/jquery-2.1.0.min.js"></script>	
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
}
?>