<?php

/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 13.11.2015
 * Time: 20:20
 * Vytvoreni instance tridy DB zavola contructor, ktery provede pripojeni do DB a zaloguje uspesne pripojeni do
 * logovaciho souboru. V pripade problemu s pripojenim provede zalogovani chyby do souboru.
 */

include "../Class/Logger.php";

class DB
{
private $hostname;
private $dbname;
private $psswd;


function __construct($hostname='mysql.plasanci.cz', $dbname='akcie-plasanci', $psswd='otuzilci')
{
    try {
        self::$conn = new PDO("mysql:host= $hostname", $dbname, $psswd);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $logger = new Logger();
        $logger->info("Pristup do DB uspesny.");
        echo "OK";
        return $conn;
    }
    catch(PDOException $e)
    {
        $logger = new Logger();
        $logger->error("Data nestazena. " . $e->getMessage() );
        echo "KO " . $e->getMessage();
    }
}



public static function queryAll($query) {
        $statement = self::executeStatement(func_get_args());
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

private static function executeStatement($params)
    {
        $query = array_shift($params);
        $statement = self::$conn->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}