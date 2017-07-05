<?php
/**
 * Created by PhpStorm.
 * User: Jarda, Vojtas
 * Date: 2.11.2015
 * Time: 20:56
 */

include "../Class/Importer.php";

$prijemce = 'plachy.jaroslav@seznam.cz,petr.vojtik@seznam.cz';
$predment = 'CRON log';
$datum = StrFTime("%d/%m/%Y %H:%M:%S", Time());
$text = 'CRON byl spusten v '.$datum .'. Spusten soubor ImporterSave.php';
mail($prijemce, $predment, $text);
$object = new Importer();
//$object->save("GE", $dateFrom, $dateTo);

$object->saveData();

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
$file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    mail($mailto, $subject, "", $header);
    }
$my_file = date('Y-m-d').'.log';
$my_path = "../Logs/";
$my_name = "Plasan&Vojtas";
$my_mail = "info@plasanci.cz";
$my_replyto = "plachy.jaroslav@seznam.cz";
$my_subject = "CRON dokoncen.";
$my_message = "Ahoj,\r\nCRON byl uspesne dokoncen. Log v priloze.\r\n\r\nPlasan&Vojtas";
$mailAddress = 'plachy.jaroslav@seznam.cz,petr.vojtik@seznam.cz';
mail_attachment($my_file, $my_path, $mailAddress, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);


