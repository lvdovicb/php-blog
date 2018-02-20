<?php


namespace simplon\dao;

class Connect {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = new \PDO('mysql:host='.$_ENV['SQL_HOST'].';dbname='.$_ENV['SQL_DATABASE'].';',
        $_ENV['SQL_USER'],
        $_ENV['SQL_PASSWORD']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }

    public static function getInstance():\PDO {
        if(Connect::$instance == null) {
            Connect::$instance = new Connect();
        }
        return Connect::$instance->pdo;
    }
    

}