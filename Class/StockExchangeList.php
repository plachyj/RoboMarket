<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 27.05.2017
 * Time: 22:04
 */

require_once "DBmysqli.php";


class StockExchangeList
{
    function getExchangeList(){
        $dbObject = new DBmysqli();
        $dbResult = $dbObject->queryAssoc('SELECT * FROM BURZA');

        if ($dbResult->num_rows > 0) {

            echo "<tr><th>BURZA_ID</th><th>Název</th><th>URL</th></tr>";

            // output data of each row

            while($row = $dbResult->fetch_assoc()) {

                echo "<tr><td>".$row["BURZA_ID"]."</td><td>".$row["NAZEV"]."</td><td>".$row["URL"]."</td></tr>";

            }



        } else {

            echo "0 results";
            $db->close();
            return $dbResult;
        }

    }
}
?>