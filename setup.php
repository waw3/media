<?php 
error_reporting(E_ERROR | E_PARSE);
require "template.php"; 
$template = new template();
$template->createPage("Simple Media Streamer");
if(!empty($_POST['username']))
{
	if(!is_dir(getcwd().'/Logs'))
	{
		$file = getcwd()."/Logs";
		if(!mkdir($file,0774,true))
		{
			print "<h2>Unable to create file Logs, please check file permissions</h2>";
		}
	}
	if(!is_dir(getcwd().'/config'))
	{
		$file = getcwd()."/config";
		if(!mkdir($file,0774))
		{
			print "<h2>Unable to create config, please check file permissions</h2>";
		}
	}
	if(!is_dir(getcwd().'/metadata'))
	{
		$file = getcwd()."/metadata";
		if(!mkdir($file,0774))
		{
			print "<h2>Unable to create metadata, please check file permissions</h2>";
		}
	}
	$username = mysql_real_escape_string($_POST['username']);
	$servername = "localhost";
	$password = mysql_real_escape_string($_POST['password']);
	if(!empty($_POST['password']))
	{
		if ( ! $conn = mysqli_connect($servername, $username, $password))
		{
			$errmsg = "<h2>Connection failed</h2>";
		}
		else
		{
			if (!$exists = mysqli_select_db($conn, "simpleMediaStreamer"))
			{
				if ($conn->query("CREATE DATABASE simpleMediaStreamer") === TRUE) 
				{
					echo "<p>Database created successfully</p>";
					$password = substr(password_hash($template->get_random_string(12),PASSWORD_DEFAULT),25);
					if ($conn->query("GRANT ALL PRIVILEGES ON simpleMediaStreamer.* TO 'smsDatabase'@'localhost' IDENTIFIED BY '$password' WITH GRANT OPTION;") === true)
					{
						echo "<p>User smsDatabase created successfully</p>";
						unlink("config/databaseUser.txt");
						$f = fopen("config/databaseUser.txt", 'a');
						fwrite($f,"smsDatabase\n");
						fwrite($f,$password);
						fwrite($f,"\nsimpleMediaStreamer");
						fclose($f);
						mysqli_select_db($conn, "simpleMediaStreamer");
						if ($conn->query("CREATE TABLE users (ID INT(32) NOT NULL AUTO_INCREMENT, username VARCHAR(64), password VARCHAR(255), firstname VARCHAR(30), lastname VARCHAR(30), regdate DATETIME, ip VARCHAR(15), userGroup VARCHAR(8), activated TINYINT, primary KEY (ID));") === true)
						{
							echo "<p>Users table created successfully</p>";
						}
						else 
						{
							echo "Error creating table: " . $conn->error;
						}
					}
					else
					{
						echo "Error creating user smsDatabase: " . $conn->error;
					}
				} 
				else 
				{
					echo "Error creating database: " . $conn->error;
				}
			}
			else
			{
				$password = substr(password_hash($template->get_random_string(12),PASSWORD_DEFAULT),25);
				if ($conn->query("grant all privileges on simpleMediaStreamer.* to 'smsDatabase'@'localhost' identified by '$password' with grant option;") === true)
				{
					echo "<p>User smsDatabase created successfully</p>";
					unlink("config/databaseUser.txt");
					$f = fopen("config/databaseUser.txt", 'a');
					fwrite($f,"smsDatabase\n");
					fwrite($f,$password);
					fwrite($f,"\nsimpleMediaStreamer");
					fclose($f);
					mysqli_select_db($conn, "simpleMediaStreamer");
					if ($conn->query("CREATE TABLE users (ID INT(32) NOT NULL AUTO_INCREMENT, username VARCHAR(64), password VARCHAR(255), firstname VARCHAR(30), lastname VARCHAR(30), regdate DATETIME, ip VARCHAR(15), userGroup VARCHAR(8), activated TINYINT, primary KEY (ID));") === true)
					{
						echo "<p>Users table created successfully</p>";
					}
					else 
					{
						echo "Error creating table: " . $conn->error;
					}
				}
				else
				{
					echo "Error creating user smsDatabase: " . $conn->error;
				}
			}
		}
	}
	else
	{
			$errmsg = "<h2>You need to set a password</h2>";
	}
}
else
{
	if(file_exists("config/databaseUser.txt"))
	{
			print "<h2>Setup has already ran, to rerun please delete the databaseUser file.</h2>".PHP_EOL;
	}
	else
	{
?>
		<h1>Setup Database</h1>
		<form action="setup.php" method="post" enctype="multipart/form-data">
	    <space>MySQL root: </space><input type="text" name="username" required/> <br />
		<space>MySQL password: </space><input type="password" name="password"/> <br />
		<input type="submit" value="Setup" name="submit" />
		<?php print $errmsg; ?>
	
		</form>
<?php
	}
}
$template->endPage(); ?>