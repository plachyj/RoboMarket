<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 28.05.2017
 * Time: 19:33
 * test git
 */

   require_once "DBmysqli.php";

   session_start();

   $user_check = $_SESSION['login_user'];

   $dbObject = new DBmysqli();

   $ses_sql = $dbObject->query("select username from admin where username = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

   $login_session = $row['username'];



   if(!isset($_SESSION['login_user'])){
       header("location:login.php");
   }
?>