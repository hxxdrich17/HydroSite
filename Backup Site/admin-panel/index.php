<!Doctype html>
<?php
session_start();
if($_SESSION['status']=="1"){
	header("Location: options.php");
	$_SESSION['logged_in']="1";
}
?>
<html>
  <head>
    <title>Admin Panel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="newstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet"> 
  </head>
  <body>
    <div class="signbox">
      <!-- <button href="index/"><h3>Home</h3></button> -->
      <h1 id="adminpanel">Admin Panel</h1><hr>
      <form name="index" id="index" action="" method="post">
        <br>
        <input placeholder="Login" title="Login" type="text" name="UID" id="UID" required>
        <!-- <br> -->
        <input placeholder="Password" title="Password" type="password" name="passwd" id="passwd" required>
        <!-- <br> -->
        <input type="submit" value="Submit" id="subm" onClick=""">
      </form>
      <form action="http://hydroplant.site">
        <input type="submit" value="Home" id="homebtn"/>
      </form>
    </div>
    
<script>
$(document).ready(function(){
	$('#form').submit(function(){
		$('#UID').fadeOut(500);
		$('#passwd').fadeOut(500);
})
});

var input = document.getElementById('UID');
input.focus();
input.select();

</script>   
  </body>
  
<?php
include('user.php');
$user_name = $_POST['UID'];
$hash = password_hash("517390", PASSWORD_DEFAULT);
$_SESSION['user']="$user_name";
$user_password = $_POST['passwd'];
$db = mysqli_connect('localhost',$user,$pass,$database) or die("Error connecting to MYSQL" . mysqli_connect_error());
$query = "SELECT password FROM $table WHERE name = '$user_name'";
mysqli_query($db, $query) or die("<p class=\"text-align:center;\">Unable to access MYSQL</p>");
$result = mysqli_query($db, $query);
$row = mysqli_fetch_array($result);
$password=$row['password'];
if(password_verify($user_password, $hash)){
        $_SESSION['status'] = "1";
        header("Location: options.php");
        die();
}else if(!empty($user_name)){
	echo "<p class=\"false\" style=\"color:red;text-align:center;font-family:'Roboto Condensed',sans-serif;font-size:48px;\">INVALID</p>" ;
}
mysqli_close($db);




?>

</html>
