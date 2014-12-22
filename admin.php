<?php 
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
require "template.php"; 
$template = new template();
$template->startSessionAdmin();
$con = $template->dbConnect();
$template->createPage("Admin control panel");
if(!empty($_POST['status']))
{
$user = $_GET['edit'];
$sql="SELECT username FROM users WHERE username = '$user'";
	if($result = mysqli_query($con,$sql))
	{
		if($row = mysqli_fetch_row($result));
		{
			$username = $row[0];

			if($_POST['status'] == "Activate")
			{
				$query="UPDATE users SET activated = 1 WHERE username = '$username';";
			}
			elseif($_POST['status'] == "Ban")
			{
				$query="UPDATE users SET activated = 2 WHERE username = '$username';";
			}
			else
			{
				$query="UPDATE users SET activated = 1 WHERE username = '$username';";
			}
			mysqli_query($con,$query);
		}
	}
}
if(!empty($_GET['edit']))
{
$username = mysqli_real_escape_string($con, strip_tags($_GET['edit']));
$sql="SELECT ID, username, firstname, lastname, regdate, userGroup , activated FROM users WHERE username = '$username'";
if($result = mysqli_query($con,$sql))
{
	if($row = mysqli_fetch_row($result));
	{
		$id = $row[0];
		$username = $row[1];
		$firstname = $row[2];
		$lastname = $row[3];
		$dateCreated = explode(" ",$row[4]);
		$userGroup = $row[5];
		$status = $row[6];
	}
}
?>
<h1>Edit User</h1>
<center>
<div class="tableDiv">
<form action="" method="post" enctype="multipart/form-data">
	<table style="margin-top: 50px; min-width: 400px; table-layout: fixed;">
	<tr></tr>
	<tr><td>First Name: </td><td><?php print $firstname; ?></td><td><input type="text" name="fname"></td></tr>
	<tr><td>Last Name: </td><td><?php print $lastname; ?></td><td><input type="text" name="lname"></td></tr>
	<tr><td>Username: </td><td><?php print $username; ?></td><td><input type="text" name="uname"></td></tr>
	<tr><td>Registration Date: </th><td><?php print $dateCreated[0]; ?></td><td></td></tr>

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
		<tr><td>Status: </td><td><?php print $label; ?></td><td><input id="button" style="background: none;" type="submit" name="status" value="<?php print $value; ?>"></td></tr>
	</center>
	</table>
	</form>
	</div>
<?php
}
else
{
$sql="SELECT ID, username, firstname, lastname, regdate, userGroup , activated FROM users";
?>
<center>
<div class="tableDiv">
	<table style="margin-top: 50px; min-width: 800px; color: black; "> 
	<th>ID</th> <th>Username</th> <th>First Name</th> <th>Last Name</th> <th>Date Created</th> <th>User Group</th> <th>Status</th> <th>Option</th>

	<?php // filling table with data from database.
	if($result = mysqli_query($con,$sql))
	{
		while($row = mysqli_fetch_array($result,MYSQLI_NUM))
		{
			$id = $row[0];
			$username = $row[1];
			$firstname = $row[2];
			$lastname = $row[3];
			$dateCreated = $row[4];
			$userGroup = $row[5];
			$status = $row[6];
			if($status == 0) 
			{
				$status = "not activated";
			}
			elseif($status == 1)
			{
				$status = '<font style="color: #66FF33">activated</font>';
			}
			else
			{
				$status = '<font style="color: red">banned</font>';
			}
			
			print "<tr>";
			print "<td>$id</td> <td>$username</td> <td>$firstname</td> <td>$lastname</td> <td>$dateCreated</td><td>$userGroup</td><td>$status</td><td><button type=\"button\" id=\"button\" style=\"background: none;\" onclick=\"javascript:location.href='admin.php?edit=$username'\">Edit</button></td>";
			print "</tr>\n";
			
		}
	}
	mysqli_close($con);
	?>
	</table>
	</div>
	</center>
<?php 
}
$template->endPage(); ?>