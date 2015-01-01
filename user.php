<?php 
if($_SERVER['SERVER_PORT'] != '443')
{
	header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit();
}
require "template.php";
$template = new template();
$template->startSessionRestricted();
$con = $template->dbConnect();
$username = $_SESSION['username'];
if(!empty($_POST['change']))
{
	$msg = $template->changePassword($username,$_POST['nPass'],
	$_POST['cPass'],$_POST['oPass']);
}
$template->createPage("User control panel");

$sql="SELECT ID, username, firstname, lastname, ".
"regdate, userGroup , activated FROM users WHERE username = ?";
$query = $con->prepare($sql);
$query->execute(array($username));
$row = $query->fetch(PDO::FETCH_ASSOC);
if(!empty($row))
{
	$id = $row['ID'];
	$username = $row['username'];
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$dateCreated = explode(" ",$row['regdate']);
}
?>
<h1>Edit User</h1>
<center>
<div class="tableDiv">
<form action="" method="post" style="margin-top: 50px; 
max-width: 350px;" enctype="multipart/form-data">
<table style="table-layout: fixed;">
<tr></tr>
<tr><td>First Name: </td><td><?php print $firstname; ?></td></tr>
<tr><td>Last Name: </td><td><?php print $lastname; ?></td></tr>
<tr><td>Username: </td><td><?php print $username; ?></td></tr>
<tr><td>Registration Date: </th>
<td><?php print $dateCreated[0]; ?></td></tr>
<tr><td>New Password: </td>
<td><input type="password" name="nPass" required/></td></tr>
<tr><td>Confirm Password: </td>
<td><input type="password" name="cPass" required/></td></tr>
<tr><td>Old Password: </td>
<td><input type="password" name="oPass" required/></td></tr>
</table>
<input id="button"  type="submit" name="change" value="Change"><br/>
</form>
<?php print $msg; ?>
</div>
	<?php
$template->endPage(); ?>