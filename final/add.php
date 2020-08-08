<?php
 session_start();
if ( ! isset($_SESSION["name"])) {
    die('ACCESS DENIED');
}


if ( isset($_POST['Cancel']))
{
    header( 'Location: index.php' ) ;
           return;
}

require_once "pdo.php";

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $y = $_POST['edu_year'.$i];
    $s = $_POST['edu_school'.$i];

    if ( strlen($y) == 0 || strlen($s) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($y) ) {
      return "Education year must be numeric";
    }
  }
  return true;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name'])&& isset($_POST['email'])&& isset($_POST['headline'])&& isset($_POST['summary']))
{
   if ( strlen($_POST['first_name']) < 1  || strlen($_POST['last_name'])< 1  ||  strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ){
  $_SESSION['failure']="All fields are required";
       header( 'Location: add.php' ) ;
           return;
         }
else if(strpos($_POST['email'],"@")===false)
    {
$_SESSION['failure'] = "Email address must contain @ ";
     header( 'Location: add.php' ) ;
           return;
    }
else if(($ans=validatePos())!==true)
{ $_SESSION['failure']=$ans;
   header( 'Location: add.php' ) ;
           return;
}   
else if(($ans=validateEdu())!==true)
{ $_SESSION['failure']=$ans;
   header( 'Location: add.php' ) ;
           return;
}  
else{
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );

$profile_id = $pdo->lastInsertId();
for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
$st = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

$st->execute(array(
  ':pid' => $profile_id,
  ':rank' => $i,
  ':year' => $_POST['year'.$i],
  ':desc' => $_POST['desc'.$i])
);
}

for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;
$st = $pdo->prepare('INSERT INTO institution (name) VALUES (:nam) ON DUPLICATE KEY UPDATE name=(:nam)');


$st->execute(array(
  ':nam' => $_POST['edu_school'.$i])
);

$school_id =$pdo->lastInsertId();

$st = $pdo->prepare('INSERT INTO education (profile_id, institution_id,rank, year) VALUES ( :pid,:iid, :r, :y)');


$st->execute(array(
  ':pid' => $profile_id,
  ':iid'=>$school_id,
  ':r' => $i,
  ':y' => $_POST['edu_year'.$i])

);
}

    $_SESSION['success']='Profile added';
    header( 'Location: index.php' ) ;
           return;
   }
 }
?>
<!DOCTYPE html>
<html>
<head>
<title>Ananya Jain</title>
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
<h1>Adding Profile for <?php $w=$_SESSION['name'];echo($w) ;?>

</h1>

<?php 
if(isset($_SESSION['failure']))
{$var=htmlentities($_SESSION['failure']);
echo"<p style='color: red;'>$var</p>";
unset($_SESSION["failure"]);}
?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea></p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<input type="submit" value="Add">
<input type="submit" name="Cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;
countEdu = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });

});

</script>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>