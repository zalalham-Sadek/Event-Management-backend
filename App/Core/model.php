<?php
namespace App\Core;


class Model {
    protected static $db;
    protected static $table;

    // نهيئ الاتصال مرة واحدة
    protected static function init() {
        if (!self::$db) {
            // $config = require __DIR__ . '/../../config.php';
            $config = [
                'host' =>  $_ENV['DB_HOST'],
                'name' => $_ENV['DB_DATABASE']  ,
                'user' =>  $_ENV['DB_USERNAME'],
                'pass' => $_ENV['DB_PASSWORD'] ,
                'charset' => $_ENV['DB_CHARSET']
            ];
            self::$db = Database::getInstance($config);
        }
    }

    public static function all() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function latest() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM " . static::$table . " ORDER BY id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM " . static::$table . " WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
        self::init(); // تأكد من وجود الاتصال
    }

}
