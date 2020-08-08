<?php
session_start();

require_once "pdo.php";
$getpara=$_GET["profile_id"];
$stmt = $pdo->query("SELECT first_name,last_name,email,headline,summary FROM Profile WHERE profile_id=$getpara");
$row=$stmt->fetch(PDO::FETCH_ASSOC);

$st=$pdo->query("SELECT position_id,rank,year,description FROM position WHERE profile_id=$getpara");
$poss=$st->fetch(PDO::FETCH_ASSOC);

$st1=$pdo->query("SELECT profile_id,rank,year,institution_id FROM education WHERE profile_id=$getpara");
$poss2=$st1->fetch(PDO::FETCH_ASSOC);

?>
<html>
<head>
<title>Ananya Jain's Profile View</title>
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
<h1>Profile information</h1>
<p>First Name: <?php echo $row["first_name"]?>
</p>
<p>Last Name: <?php echo $row["last_name"]?>
</p>
<p>Email: <?php echo $row["email"]?>
</p>
<p>Headline:<br/><?php echo $row["headline"]?></p>
<p>Summary:<br/><?php echo $row["summary"]?>
</p>
<p>
<?php
if($poss!==false)
{ echo("Position<br><ul>");
	do
	{echo("<li>".$poss["year"].":".$poss["description"]."</li>");
    }while($poss = $st->fetch(PDO::FETCH_ASSOC) ) ;
}
echo("</ul>")
?>
</p>

<p>
<?php
if($poss2!==false)
{ echo("Education<br><ul>");
  do
  {
    $id=$poss2['institution_id'];
$st2=$pdo->query("SELECT name FROM institution WHERE institution_id=$id");
$poss3=$st2->fetch(PDO::FETCH_ASSOC);
    echo("<li>".$poss2["year"].":".$poss3["name"]."</li>");
    }while($poss2 = $st1->fetch(PDO::FETCH_ASSOC) ) ;
}
echo("</ul>")
?>
</p>

<a href="index.php">Done</a>
</div>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>
