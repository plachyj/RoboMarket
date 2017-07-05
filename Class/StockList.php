<?php

/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 14.04.2017
 * Time: 21:07
 */

require_once "DBmysqli.php";


class StockList
{
    function getStockList(){
        $dbObject = new DBmysqli();
        $dbResult = $dbObject->queryAssoc('SELECT * FROM TITUL');

            if ($dbResult->num_rows > 0) {

                echo "<tr><th>TITUL_ID</th><th>Název</th></tr>";

                // output data of each row

                while($row = $dbResult->fetch_assoc()) {

                    echo "<tr><td>".$row["TITUL_ID"]."</td><td>".$row["NAZEV"]."</td></tr>";

                }



            } else {

                echo "0 results";
                $db->close();
                return $dbResult;
            }

    }
}
?>
