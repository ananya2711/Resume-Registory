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
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
           return;
         }
else if(strpos($_POST['email'],"@")===false)
    {
$_SESSION['failure'] = "Email address must contain @ ";
     header("Location: edit.php?profile_id=".$_POST['profile_id']);
           return;
    }
  else if(($ans=validatePos())!==true)
{ $_SESSION['failure']=$ans;
 header("Location: edit.php?profile_id=".$_POST['profile_id']);
           return;
}   
else if(($ans=validateEdu())!==true)
{ $_SESSION['failure']=$ans;
   header( 'Location: add.php' ) ;
           return;
}  
else{
 $pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $st = $pdo->prepare('UPDATE Profile SET user_id = :uid, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pf');

        $st->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'], 
          ':pf' =>$_POST['profile_id'],
        ));


  $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
$stmt->execute(array( ':pid' => $_POST['profile_id']));

 $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
$stmt->execute(array( ':pid' => $_POST['profile_id']));

$profile_id = $_POST['profile_id'];
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

    $_SESSION['success'] = 'Record updated';
    header( 'Location:index.php' ) ;
    return;
   }
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['fail'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

if ( isset($_POST['Save']) && !(isset($_SESSION['failure'])))
{
   header("Location: edit.php?profile_id=".$_POST['profile_id']);
           return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$sum = htmlentities($row['summary']);
$profile_id = $row['profile_id'];


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
<script>countPos=0;</script>
<div class="container">
<h1>Editing Profile for <?php $w=$_SESSION['name'];echo($w) ;?>

</h1>
<?php 
if(isset($_SESSION['failure']))
{$var=htmlentities($_SESSION['failure']);
echo"<p style='color: red;'>$var</p>";
unset($_SESSION["failure"]);}
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $fn ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $ln ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $em ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $he ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $sum ?></textarea>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>"></p>
<p>
  Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
  <?php
      $getpara=$_GET["profile_id"];
      $st=$pdo->query("SELECT position_id,rank,year,description FROM position WHERE profile_id=$getpara");
      $poss=$st->fetch(PDO::FETCH_ASSOC);
      $ctr=0;
      if($poss!==false)
      {
        do
        {$ctr++;
        echo('<div id="position'.$ctr.'"><p>Year: <input type="text" name="year'.$ctr.'"');
        echo('value="'.$poss['year'].'"/>');
       echo('<input type="button" value="-" onclick="');
       echo("$('#position".$ctr);
       echo("').remove()&& --countPos;return false;\"></p>");
        echo('<p><textarea name="desc'.$ctr.'" rows="8" cols="80">'.$poss["description"].'</textarea>
            </div>');

        }while($poss = $st->fetch(PDO::FETCH_ASSOC) ) ;
      }

  ?>
</div>
</p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
  <?php
      $getpara=$_GET["profile_id"];
      $st=$pdo->query("SELECT institution_id,rank,year FROM education WHERE profile_id=$getpara");
      $poss=$st->fetch(PDO::FETCH_ASSOC);
      $ectr=0;
      if($poss!==false)
      {
        do
        {$ectr++;
          $iit=$poss["institution_id"];
      $sta=$pdo->query("SELECT name FROM institution WHERE institution_id=$iit");
      $poss2=$sta->fetch(PDO::FETCH_ASSOC);
        echo('<div id="edu'.$ectr.'"><p>Year: <input type="text" name="edu_year'.$ctr.'"');
        echo('value="'.$poss['year'].'"/>');
       echo('<input type="button" value="-" onclick="');
       echo("$('#edu".$ectr);
       echo("').remove()&& --countEdu;return false;\"></p>");
        echo('<p>School: <input type="text" class="school" name="edu_school'.$ectr.'"');
        echo('value="'.$poss2['name'].'"/></div>');

        }while($poss = $st->fetch(PDO::FETCH_ASSOC)) ;
      }

  ?>
</div>
</p>
<p>
<input type="submit" value="Save">
<input type="submit" name="Cancel" value="Cancel">
</p>
</form>
</div>

<script>
countPos+= <?php echo("$ctr");?>;
countEdu = <?php echo("$ectr");?>;

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
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>