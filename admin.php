<?php 
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
include "template.php"; 
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

			if($_POST['status'] == "activate")
			{
				$query="UPDATE users SET activated = 1 WHERE username = '$username';";
			}
			elseif($_POST['status'] == "ban")
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
<div id = "contentWrapper">
	<form action="admin.php?edit=<?php print $username; ?>" method="post" enctype="multipart/form-data" style="margin-left: 25px; width: 300px;" >
		<p style="text-align: left;">First Name: <?php print $firstname; ?></p>
		<p style="text-align: left;">Last Name: <?php print $lastname; ?></p>
		<p style="text-align: left;" >Username: <?php print $username; ?></p>
		<p style="text-align: left;">Registration Date: <?php print $dateCreated[0]; ?></p>
		<?php if($status == 0) 
		{ 
			$value = "activate";
		}
		elseif($status == 1)
		{
			$value = "ban";
		}
		else
		{
			$value = "unban";
		}
		?>
		<p style="float: left; padding: 0px"><input id="button" style="margin-left: 0px;" type="submit" name="status" value="<?php print $value; ?>"></p>
	</form>
	</div>
<?php
}
else
{
$sql="SELECT ID, username, firstname, lastname, regdate, userGroup , activated FROM users";
?>
<center>
	<table border="1" style="margin-top: 50px;"> 
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
				$status = "activated";
			}
			else
			{
				$status = "banned";
			}
			
			print "<tr>";
			print "<td>$id</td> <td>$username</td> <td>$firstname</td> <td>$lastname</td> <td>$dateCreated</td><td>$userGroup</td><td>$status</td><td><button type=\"button\" onclick=\"javascript:location.href='admin.php?edit=$username'\">Edit</button></td>";
			print "</tr>\n";
			
		}
	}
	mysqli_close($con);
	?>
	</table>
	</center>
<?php 
}
$template->endPage(); ?>