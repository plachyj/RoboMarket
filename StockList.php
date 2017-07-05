<?php
include "Class/StockList.php";
include "Class/StockExchangeList.php";
?>
<!DOCTYPE html>
<html lang="cz">
<html>
<title>W3.CSS</title>
<meta charset="UTF-8">
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


<div class="w3-container w3-margin">
    <button onclick="myFunction('SeznamAkcii')" class="w3-btn w3-block w3-gray w3-left-align w3-card-4">Seznam akcií...</button>
    <div id="SeznamAkcii" class="w3-container w3-hide">

    <table class="w3-table-all w3-card-4">
    <?php
    $stockList = new StockList();
    $stockList->getStockList();
    ?>
        </table>
    </div>

    <button onclick="myFunction('SeznamBurz')" class="w3-btn w3-block w3-gray w3-left-align w3-card-4">Seznam burz...</button>
    <div id="SeznamBurz" class="w3-container w3-hide">

        <table class="w3-table-all w3-card-4">
            <?php
            $exchangeList = new StockExchangeList();
            $exchangeList->getExchangeList();
            ?>
        </table>
    </div>
</div>



<div class="w3-container w3-teal">
    <p>Plavoj corporation
        <br>Brewery Market Soft

    </p>

</div>
<script>
    function myFunction(id) {
        var x = document.getElementById(id);
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
        } else {
            x.className = x.className.replace(" w3-show", "");
        }
    }
</script>
</body>
</html>