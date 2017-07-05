<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 13.11.2015
 * Time: 20:30
 */


include "../Class/DBmysqli.php";
include "../Class/Logger.php";

$titulID = 1;
$dateTo = '2015-11-20';
$object = new DBmysqli();
$insert = "INSERT INTO DATA (DATA_ID, TITUL_ID, OPEN, CLOSE, HIGH, LOW, VOLUME, DATE) VALUES (NULL, ".$titulID.", '30.0','30.0','30.0','30.0','28','".$dateTo."')";
echo $insert;
$object->insert($insert);
$result = $object->query('SELECT T.NAZEV, T.KOD, B.URL, B.BURZA_ID
                          FROM TITUL T, BURZA B
                          WHERE T.BURZA_ID = B.BURZA_ID ');


foreach($result as $row=>$value)
{
    echo ($row .': NAZEV:  ' . $value[0]  .' , KOD: ' .$value[1]. ' , URL: ' .$value[2] . ' , burza id: ' . $value[3] .'<br />');
    $titulKOD = $value[1];
    $burzaURL = $value[2];
    $burzaID = $value[3];

    switch($burzaID)
    {
        case 1:

            echo "burza id 1";
            $url = str_replace("*titul", $titulKOD, $burzaURL);
            echo $url;
            break;
        case 2:
            echo "burza id 2";
            break;
        default:
            $logger = new Logger();
            $logger->error("Burza nenalezena. Zkontroluj zda nebyla pridana burza bez upravy php");
    }
}





/**
 * Automaticke presmerovani stranku na stranku uvedenou v Location
 * header("Location: http://akcie.plasanci.cz");
 * header("Connection: close");
 */
?>
<br>
<a href="../index.html">Zp�t</a>
