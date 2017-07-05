<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 15.11.2015
 * Time: 19:15
 * Wrapper pro mysqli, connect do dB se provadi pri kazdem zavolani metody query. Metoda query je pro spusteni\
 * slectu. Metoda query vraci pole, jednotlive sloupecky jsou podle poradi pod id 0...n.
 */



class DBmysqli{
    private $db_hostname;
    private $db_database;
    private $db_username;
    private $db_password;

    public function __construct($db_hostname='mysql.plasanci.cz', $db_username='akcie-plasanci', $db_password='otuzilci', $db_database='akcie-plasanci')
    {
        $this->db_hostname = $db_hostname;
        $this->db_database = $db_database;
        $this->db_username = $db_username;
        $this->db_password = $db_password;

    }

    public function connect()
    {
        $conn =  new mysqli($this->db_hostname, $this->db_username, $this->db_password, $this->db_database);
        if (mysqli_connect_errno())
        {

            $e = mysqli_connect_error();
            $log = new Logger();
            $log->error("Chybe ve spojeni s DB " . $e);

            echo "chyba ve spojeni s DB";
        }
        $conn->set_charset("utf8");
        return $conn;
    }

    public function query($query)
    {
       $db = $this->connect();
       $result = $db->query($query);

       $results = $result->fetch_all();
       $db->close();
       return $results;
    }

    public function queryAssoc($query)
    {
        $db = $this->connect();
        $result = $db->query($query);
        $db->close();
        return $result;


    }


    public function insert($insert)
    {
        $db = $this->connect();
        echo 'db connected <br />';
        $db->query($insert);
        echo 'row inserted <br />';
        $db->commit();
        $db->close();
    }

    public function count($query)
    {
        $db = $this->connect();
        $result = $db->query($query);
        $rowCount= $result->num_rows;
        $db->close();
        return $rowCount;
    }
}


?>