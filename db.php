<?php 

$con = new PDO("mysql:host=localhost;dbname=douban",'root','123456');
$con->query("SET NAMES utf8");

return $con;