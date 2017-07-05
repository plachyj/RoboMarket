<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 27.05.2017
 * Time: 22:38
 */

require_once "DBmysqli.php";


class LastDayData
{
    function getLastDayData(){
        $dbObject = new DBmysqli();

        $dbResult = $dbObject->queryAssoc('SELECT TITUL.NAZEV, DATA.OPEN, DATA.CLOSE, DATA.HIGH, DATA.LOW, DATA.VOLUME, DATA.DATE FROM  DATA, TITUL
                                            WHERE DATA.DATE = (SELECT MAX(DATE) FROM  DATA) AND
                                            DATA.TITUL_ID = TITUL.TITUL_ID');

        if ($dbResult->num_rows > 0) {

            echo "<tr><th>Titul Název</th><th>OPEN</th><th>CLOSE</th><th>HIGH</th><th>LOW</th><th>VOLUME</th><th>Datum</th></tr>";

            // output data of each row

            while($row = $dbResult->fetch_assoc()) {

                echo "<tr>
                        <td>".$row["NAZEV"]."</td>
                        <td>".$row["OPEN"]."</td>
                        <td>".$row["CLOSE"]."</td>
                        <td>".$row["HIGH"]."</td>
                        <td>".$row["LOW"]."</td>
                        <td>".$row["VOLUME"]."</td>
                        <td>".$row["DATE"]."</td>
                      </tr>";

            }



        } else {

            echo "0 results";
            $db->close();
            return $dbResult;
        }

    }
}
?>