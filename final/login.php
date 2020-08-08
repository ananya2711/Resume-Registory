<?php // Do not put any HTML above this line
 session_start();
require_once "pdo.php";


if ( isset($_POST['Cancel']))
{
    header( 'Location:index.php' ) ;
           return;
}

$salt = 'XyZzy12*_';
$failure = false;  // If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    $check = hash('md5', $salt.$_POST['pass']);
$stmt = $pdo->prepare('SELECT user_id, name FROM users
    WHERE email = :em AND password = :pw');
$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row !== false ) {
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
   }
   else{
    $_SESSION['failure'] ="Invalid Password";
      header("Location: login.php");
    return;
   }
}
// Fall through into the View
?>

<!DOCTYPE html>
<html>
<head>
<title>Ananya Jain's Login Page</title>
<!-- bootstrap.php - this is HTML -->
<?php
 if(isset($_SESSION['success']))
{$de=$_SESSION['success'];
  echo"<p style='color: green;'>$de</p>";
 unset($_SESSION["success"]);}

if(isset($_SESSION['fail']))
{$de=$_SESSION['fail'];
  echo"<p style='color:red;'>$de</p>";
 unset($_SESSION["fail"]);}


?>

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
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION["failure"]) ) {
        echo('<p style="color:red">'.htmlentities($_SESSION["failure"])."</p>\n");
        unset($_SESSION["failure"]);
    }
?>

<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidateemail()&&dovalidatepass();" value="Log In">
<input type="submit" name="Cancel" value="Cancel">
</form>

<script>
function doValidateemail() {
    console.log('Validating email...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

function doValidatepass() {
    console.log('Validating password...');
    try {
        pw = document.getElementById('id_1723').value;
        console.log("Validating pw="+pw);
        if (pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

</script>

</div>
</body>

</body>
