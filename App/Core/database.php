<?php
namespace App\Core;

use PDO;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct($config) {
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
        $this->connection = new PDO($dsn, $config['user'], $config['pass']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance($config) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance->connection;
    }
}
