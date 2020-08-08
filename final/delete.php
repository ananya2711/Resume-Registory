<?php
session_start();

require_once "pdo.php";

if ( isset($_POST['Cancel']))
{
    header( 'Location:index.php' ) ;
           return;
}

if ( isset($_POST['Delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location:index.php' ) ;
    return;
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['fail'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$getpara=$_GET["profile_id"];
$st = $pdo->prepare("SELECT first_name,last_name FROM Profile WHERE profile_id=$getpara");
$st->execute(array(":xyz" => $_GET['profile_id']));
$row = $st->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['fail'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Ananya Jain's Profile</title>
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
<h1>Deleteing Profile</h1>
<form method="post" action="delete.php">
<p>First Name: <?php echo $row["first_name"]?>
</p>
<p>Last Name:
<?php echo $row["last_name"]?></p>
<input type="hidden" name="profile_id" value=<?php echo "$getpara"?>
>
<input type="submit" name="Delete" value="Delete">
<input type="submit" name="Cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>
