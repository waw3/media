<?php 
require "vendor/autoload.php";
$Core = new Core();
$Core->requireSSL();
$Core->startSessionRestricted();
$con = $Core->dbConnect();
$username = $_SESSION['username'];
if(!empty($_POST['change']))
{
	$msg = $Core->changePassword($username,$_POST['nPass'],$_POST['cPass'],$_POST['oPass']);
}
$Core->createPage("User control panel");

$sql="SELECT ID, username, firstname, lastname, regdate, userGroup , activated FROM users WHERE username = ?";
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
echo '<h1 id="title"style="font-size: 24px;">User Control Panel</h1>'.PHP_EOL;		
?>
<center>
<div class="tableDiv">
<form action="" method="post" style="margin-top: 50px; max-width: 350px; color: red;" enctype="multipart/form-data">
<table style="table-layout: fixed;">
<tr></tr>
<tr><td>First Name: </td><td><?php print $firstname; ?></td></tr>
<tr><td>Last Name: </td><td><?php print $lastname; ?></td></tr>
<tr><td>Username: </td><td><?php print $username; ?></td></tr>
<tr><td>Registration Date: </th><td><?php print $dateCreated[0]; ?></td></tr>
<tr><td>New Password: </td><td><input style="color: black;" type="password" name="nPass" required/></td></tr>
<tr><td>Confirm Password: </td><td><input style="color: black;" type="password" name="cPass" required/></td></tr>
<tr><td>Old Password: </td><td><input style="color: black;" type="password" name="oPass" required/></td></tr>
</table>
<input id="button" style="float: right;" type="submit" name="change" value="Change"><br/>
</form>
<?php print $msg; ?>
</div>
	<?php
$Core->endPage(); ?>