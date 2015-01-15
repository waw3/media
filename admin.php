<?php 

require "vendor/autoload.php";
$core = new core();
$core->requireSSL();
$core->startSessionAdmin();
$con = $core->dbConnect();
$core->createPage("Admin control panel","adminMenu");
$msg = "";
//Changes the user's activated status.

if(!empty($_POST['update']))
{
	$user = $_GET['edituser'];
	$sqlQuery = new sql("users", $core->dbConnect());
	$col = array();
	$vars = array();
	if($_POST['nPass'] != $_POST['cPass'])
	{
		$msg = "<h2>Passwords do not match<h2>";
	}
	else
	{
		if(!empty($_POST['uname']))
		{
			array_push($col,"username");
			array_push($vars,$_POST['uname']);
		}
		if(!empty($_POST['nPass']))
		{
			array_push($col,"password");
			$pass=password_hash($_POST['nPass'],PASSWORD_DEFAULT);
			array_push($vars,$pass);
		}
		if(!empty($_POST['fname']))
		{
			array_push($col,"firstname");
			array_push($vars,$_POST['fname']);
		}
		if(!empty($_POST['lname']))
		{
			array_push($col,"lastname");
			array_push($vars,$_POST['lname']);
		}
		array_push($vars,$user);
		$col = implode($col," ");
		$vars = implode($vars," ");
		$sqlQuery->update($col,"username",$vars);
		if(!empty($_POST['uname']))
		{
			$user = $_POST['uname'];
			header("Location: admin.php?edituser=$user");
		}
	}
}

if(!empty($_POST['status']))
{

	$user = $_GET['edituser'];
	$sqlQuery = new sql("users", $core->dbConnect());
	$row = $sqlQuery->select("id username userGroup","username",$user);
	$username = $row[0]['username'];
	$group = $row[0]['userGroup'];
	$id = $row[0]['id'];
	if($group == "admin" || $id == 0) { $msg = "<h2>Cannot ban admin</h2>"; }
	else
	{
		if($_POST['status'] == "Activate")
		{
			$sqlQuery->update("activated","username","1 $username");
		}
		elseif($_POST['status'] == "Ban")
		{
			$sqlQuery->update("activated","username","2 $username");
		}
		else
		{
			$sqlQuery->update("activated","username","1 $username");
		}
	}
}

//Shows information on a single user.
if(isset($_GET['edit']) && $_GET['edit'] == "settings")
{
	$mVal = $_POST['mDir'];
	$sVal = $_POST['sDir'];
	$muVal = $_POST['muDir'];
	$ssl = $_POST['val'];
	if(isset($_POST['mCheck']))
	{
		if(file_exists($_POST['mDir']))
		{
			$msg = "<h3>".$_POST['mDir']." Directory exists</h3>";
		}
		else
		{
			$msg = "<h2>Directory does not exist</h2>";
		}	
	}
	if(isset($_POST['sCheck']))
	{
		if(file_exists($_POST['sDir']))
		{
			$msg = "<h3>".$_POST['sDir']." Directory exists</h3>";
		}
		else
		{
			$msg = "<h2>Directory does not exist</h2>";
		}
	}
	if(isset($_POST['muCheck']))
	{
		if(file_exists($_POST['muDir']))
		{
			$msg = "<h3>".$_POST['muDir']." Directory exists</h3>";
		}
		else
		{
			$msg = "<h2>Directory does not exist</h2>";
		}
	}
	if(isset($_POST['submit']))
	{
		$configText = array();
		if(file_exists($_POST['mDir']))
		{
			if(file_exists("movies")){exec('rm -rf movies');}
			$dir = escapeshellarg($_POST['mDir']);
			$path = getcwd()."/movies";
			$msg = shell_exec("ln -sf $dir $path");
			$configText['movieDir'] = $dir;
		}
		else if(!empty($_POST['mDir']))
		{
			$msg .= "<h2>".$_POST['mDir']." Directory does not exist</h2>";
		}
		if(file_exists($_POST['sDir']) && !empty($_POST['sDir']))
		{
			if(file_exists("shows")){exec('rm -rf shows');}
			$dir = escapeshellarg($_POST['sDir']);
			$path = getcwd()."/shows";
			$msg = shell_exec("ln -sf $dir $path");
			$configText['showDir'] = $dir;
		}
		else if(!empty($_POST['sDir']))
		{
			$msg .= "<h2>".$_POST['sDir']." Directory does not exist</h2> ";
		}
		if(file_exists($_POST['muDir']) && !empty($_POST['muDir']))
		{
			if(file_exists("music/public")){exec('rm -rf music/public');}
			$dir = escapeshellarg($_POST['muDir']);
			$path = getcwd()."/music/public";
			$msg = shell_exec("ln -sf $dir $path");
			$configText['musicDir'] = $dir;
		}
		else if(!empty($_POST['muDir']))
		{
			$msg .= "<h2>".$_POST['muDir']." Directory does not exist</h2> ";
		}
		if($_POST['val'] == "on"){$sslon = "checked";}
		else{$ssloff = "checked";}
		if(is_numeric($_POST['quality']))
		{
			$configText['bitrate'] = $_POST['quality'];
			$bVal = $_POST['quality'];
		}
		else if(!empty($_POST['quality']))
		{
			$msg .= "<h2>Bitrate has to be a number.</h2> ";
		}
		$configText['ssl'] = $_POST['val'];
		$configText = json_encode($configText);
		$f = fopen("config/config.json", 'w');
		fwrite($f,$configText);
		fclose($f);
	}
		
		if(!isset($_POST['mCheck']) && !isset($_POST['sCheck']) && !isset($_POST['mCheck']) && !isset($_POST['val']))
		{
			$config = $core->configInfo();
			$mVal = str_replace("'", "",$config['movieDir']);
			$sVal = str_replace("'", "",$config['showDir']);
			$muVal = str_replace("'", "",$config['musicDir']);
			$bVal = $config['bitrate'];
			if($config['ssl'] == "on"){$sslon = "checked";}
			else{$ssloff = "checked";}
		}
		
?>
<center>
	<div class="tableDiv">
	<form action="" method="post" style="max-width: 400px;" 
	enctype="multipart/form-data">
	<table style="margin-top: 50px; min-width: 400px; color: black; ">
	<tr></tr>
	<tr><td>Movie Directory: </td><td><input type="text" value="<?php print $mVal;?>" name="mDir"/>
	<input id="button" style="background: none;" type="submit" value="Check" 
	name="mCheck" /></td></tr>
	<tr><td>Show Directory: </td><td><input type="text" value="<?php print $sVal;?>" name="sDir"/>
	<input id="button" style="background: none;" type="submit" value="Check" 
	name="sCheck" /></td></tr>
	<tr><td>Music Directory: </td><td><input type="text" value="<?php print $muVal;?>" name="muDir"/>
	<input id="button" style="background: none;" type="submit" value="Check" 
	name="muCheck" /></td></tr>
	<tr><td>Max Bitrate(kbps): </td><td><input type="text" name="quality" 
	size="4" value="<?php print $bVal;?>"/></td></tr>
	<tr><td>Enable https </td><td><input type="radio" name="val" value="on" <?php print $sslon;?>>On
	<input type="radio" name="val" value="off" <?php print $ssloff;?>>Off</td>
	</table>
	<input id="button" type="submit" value="Submit" name="submit" /><br>
	<?php print $msg; ?>
	</form>
		
	</div>
	</center>
<?php
}
else if(isset($_GET['edit']) && $_GET['edit'] == "list")
{

	$sql='SELECT ID, username, firstname, lastname, regdate, '.
	'userGroup, activated FROM users';
	$query = $con->query($sql);
	$con = null;
	?>
	<center>
	<div class="tableDiv">
	<table style="margin-top: 50px; min-width: 800px; color: black; "> 
	<th>ID</th> <th>Username</th> <th>First Name</th> <th>Last Name</th> 
	<th>Date Created</th> <th>User Group</th> <th>Status</th> <th>Option</th>
	<?php // filling table with data from database.
	while($row = $query->fetch(PDO::FETCH_ASSOC))
	{
		$id = $row['ID'];
		$username = $row['username'];
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$dateCreated =$row['regdate'];
		$userGroup = $row['userGroup'];
		if($row['activated'] == 0) 
		{
			$status = "not activated";
		}
		elseif($row['activated'] == 1)
		{
			$status = '<font style="color: #66FF33">activated</font>';
		}
		else
		{
			$status = '<font style="color: red">banned</font>';
		}	
		print "<tr>";
		print "<td>$id</td> <td>$username</td> <td>$firstname</td><td>$lastname</td>".
		" <td>$dateCreated</td><td>$userGroup</td><td>$status</td><td>".
		"<button type=\"button\""." id=\"button\" style=\"background: none;\" ".
		"onclick=\"javascript:location.href='admin.php?edituser=$username'\">Edit</button></td>";
		print "</tr>\n";
			
	}
	?>
	</table>
	</div>
	</center>
<?php
}
else if(!empty($_GET['edituser']))
{
	$con = $core->dbConnect();
	$sqlQuery = new sql("users",$con);
	$username = $_GET['edituser'];
	$row = $sqlQuery->select("ID username firstname lastname regdate".
	" userGroup activated","username",$username);
	if(!empty($row))
	{
			$id = $row[0]['ID'];
			$username = $row[0]['username'];
			$firstname = $row[0]['firstname'];
			$lastname = $row[0]['lastname'];
			$dateCreated = explode(" ",$row[0]['regdate']);
			$userGroup = $row[0]['userGroup'];
			$status = $row[0]['activated'];
	}
?>
	<h1>Edit User</h1>
	<center>
	<div class="tableDiv">
	<form action="" method="post" style="max-width: 500px;" 
	enctype="multipart/form-data">
	<table style=" min-width: 500px; table-layout: fixed;">
	<tr></tr>
	<tr><td>First Name: </td><td><?php print $firstname; ?></td>
	<td><input type="text" name="fname"></td></tr>
	<tr><td>Last Name: </td><td><?php print $lastname; ?></td>
	<td><input type="text" name="lname"></td></tr>
	<tr><td>Username: </td><td><?php print $username; ?></td>
	<td><input type="text" name="uname"></td></tr>
	<tr><td>New Password: </td><td></td>
	<td><input type="password" name="nPass"/></td></tr>
	<tr><td>Confirm Password: </td><td></td>
	<td><input type="password" name="cPass"/></td></tr>
	<tr><td>Registration Date: </th><td><?php print $dateCreated[0]; ?></td>
	<td></td></tr>

	<?php if($status == 0) 
	{ 
		$value = "Activate";
		$label = "Inactive";
	}
	elseif($status == 1)
	{
		$value = 'Ban';
		$label = '<font style="color: #66FF33">Active</font>';
	}
	else
	{
		$value = "Unban";
		$label = '<font style="color: red">Banned</font>';
	}
	?>
	<tr><td>Status: </td><td><?php print $label; ?></td>
	<td><input id="button" 
	style="background: none;" type="submit" name="status" 
	value="<?php print $value; ?>"></td></tr>
	</center>
	</table>
	<input id="button" type="submit" name="update" value="Update"><br/>
	<?php print $msg; ?>
	</form>
	</div>
<?php
}
//Shows the list of currently registered users.
$core->endPage(); ?>