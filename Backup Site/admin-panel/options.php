<!Doctype Html>
<?php
session_start();
$status = $_SESSION['status'];
if($status == "1"){
	if($_SESSION['logged_in']=="1"){
		echo "<p style=\"color:red;\"><b>You are already logged in.</b></p>";
		$_SESSION['logged_in']="0";
}}else if($status != "1"){
        header("Location: 404.php");}
include "user.php";
?>
<html>
<head>
<title>Admin Panel</title>
<link rel="stylesheet" type="text/css" href="newstyle.css">
<meta HTTP-EQUIV="refresh" CONTENT="300;URL=logout.php">
</head>
<body>
<h1 style="text-align: center;">Admin Panel</h1>
<div id="blueLink">
<a href="settings/settings.php">Settings</a>
&ensp;<a href="logout.php">Logout</a><hr>
</div>

<a href="shell/simple-shell.php"><p class="server">EXECUTE SHELL</p></a>
<a href="mysql_exec.php"><p class="server">EXECUTE MYSQL</p></a>
<a href="php_exec.php"><p class="server">EXECUTE PHP</p></a>
<a href="installers/index.php"> <p class="server"> INSTALLERS </p></a> <br><br>

</body>


</html>
