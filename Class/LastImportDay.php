<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 27.05.2017
 * Time: 23:17
 */

require_once "DBmysqli.php";


class LastImportDate
{
    function getLastImportDay(){
        $dbObject = new DBmysqli();

        $dbResult = $dbObject->queryAssoc('SELECT MAX(DATE) as DATE FROM  DATA');

        if ($dbResult->num_rows > 0) {

            $row = $dbResult->fetch_assoc();
            echo $row["DATE"];

        }
        else {
            echo "0 results";

            return $dbResult;
        }

    }
}
?>