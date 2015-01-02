<?php 
require "template.php";
$template = new template();
$template->startSessionAdmin();
$con = $template->dbConnect();
$template->createPage("Admin control panel");
$template->adminMenu();
$msg = "";
//Changes the user's activated status.
if(!empty($_POST['status']))
{

	$user = $_GET['edit'];
	$sqlQuery = new sql("users", $template->dbConnect());
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
if($_GET['edit'] == "settings")
{
?>
<center>
	<div class="tableDiv">
	<form action="" method="post" style="max-width: 500px;" 
	enctype="multipart/form-data">
	<table style="margin-top: 50px; min-width: 400px; color: black; ">
	<tr></tr>
	<tr><td>Max Bitrate(kbps): </td><td><input type="number" name="quality"/></td>
	<tr><td>Enable https </td><td><input type="radio" name="val" value="on">On
	<input type="radio" name="val" value="off">Off</td>
	</table>
	</form>
	</div>
	</center>
<?php
}
else if($_GET['edit'] == "list")
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
		print "<td>$id</td> <td>$username</td> <td>$firstname</td> <td>$lastname</td>".
		" <td>$dateCreated</td><td>$userGroup</td><td>$status</td><td>".
		"<button type=\"button\""." id=\"button\" style=\"background: none;\" ".
		"onclick=\"javascript:location.href='admin.php?edit=$username'\">Edit</button></td>";
		print "</tr>\n";
			
	}
	?>
	</table>
	</div>
	</center>
<?php
}
else if(!empty($_GET['edit']))
{
	$sqlQuery = new sql("users", $template->dbConnect());
	$username = $_GET['edit'];
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
	<form action="" method="post" style="max-width: 500px;" enctype="multipart/form-data">
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
$template->endPage(); ?>