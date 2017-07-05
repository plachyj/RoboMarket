<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 28.05.2017
 * Time: 19:46
 */

require_once "Class/DBmysqli.php";
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form

    $dbObject = new DBmysqli();
    $db = $dbObject->connect();

    $myusername = mysqli_real_escape_string($db,$_POST['username']);
    $mypassword = mysqli_real_escape_string($db,$_POST['password']);

    $sql = "SELECT id FROM admin WHERE username = '$myusername' and passcode = '$mypassword'";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $active = $row['active'];

    $count = mysqli_num_rows($result);

    // If result matched $myusername and $mypassword, table row must be 1 row

    if($count == 1) {
        session_register("myusername");
        $_SESSION['login_user'] = $myusername;

        header("location: welcome.php");
    }else {
        $error = "Your Login Name or Password is invalid";
    }
}
?>

<!DOCTYPE html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .material-icons {vertical-align:-14%}
</style>
<body>

<div class="w3-container w3-teal">
    <h1>Robo Market</h1>
    <div class="w3-padding w3-display-topright">Příhlášen: guest
        <br>
        <a href="#">Login</a>
        <br>
        <a href="#">Registrace</a>
    </div>
</div>
<div class="w3-padding w3-xlarge w3-teal">
    <a href="index.php" class="w3-bar-item w3-button"><i class="fa fa-home"></i></a>
    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-search"></i></a>
    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-arrow-left"></i></a>
    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-arrow-right"></i></a>
</div>

<div class="w3-bar w3-teal">

    <a href="StockList.php" class="w3-bar-item w3-button">Číselníky</a>
    <a href="#" class="w3-bar-item w3-button">Portfolio</a>
    <a href="#" class="w3-bar-item w3-button">Burzy</a>
    <a href="Log.php" class="w3-bar-item w3-button">Logy</a>
</div>


<div class="w3-container">
    <p>Automatizované stahování dat ze světových burz a jejich analýza.</p>


</div>



<div class="w3-container w3-teal w3-bottom">
    <p>Plavoj corporation
        <br>Brewery Market Soft

    </p>

</div>

</body>
</html>