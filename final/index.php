<?php
    session_start();
    require_once "pdo.php";
    ?>

<!DOCTYPE html>
<html>
<head>
<title>Ananya Jain</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<h1>Ananya Jain's Resume Registry</h1>
<?php


if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}

if ( isset($_SESSION['fail']) ) {
    echo '<p style="color:red">'.$_SESSION['fail']."</p>\n";
    unset($_SESSION['fail']);
}

 if(!isset($_SESSION['name'])) 
     echo '<p><a href="login.php">Please log in</a></p>';
 else
    {
    	echo '<p><a href="logout.php">Logout</a></p>';
}
 ?>

<?php 

$stmt = $pdo->query("SELECT first_name,last_name,headline,user_id,profile_id FROM Profile");
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row!==false)
{
  echo('<table class="table">'."\n");
echo('<thead><tr><th>Name</th>
<th>Headline</th>');
if(isset($_SESSION["name"]))
   echo('<th>Action</th>');
echo('</tr></thead>');

do{
	echo "<tr><td>";
  echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row["first_name"]." ".$row["last_name"]).'</a>');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td><td>");
    if(isset($_SESSION["user_id"]))
    if($row['user_id']==$_SESSION["user_id"])
    {echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');}

    echo("</td></tr>\n");
}while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) ;
echo('</table>');
}
else
{
  echo("<p>No rows found</p>");
}

if(isset($_SESSION["name"]))
  echo('<p><a href="add.php">Add New Entry</a></p>');
?>
</div>
</body>
</html>


