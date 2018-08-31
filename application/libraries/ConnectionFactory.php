<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ConnectionFactory {
    
    private $host= "localhost";
    private $dbname= "brasileirao";
    private $user= "root";
    private $password= "";


    public function getConnection() {
        try {
            $pdo = new PDO("mysql:host=$this->host; dbname=$this->dbname", $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

}
