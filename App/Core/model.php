<?php
namespace App\Core;

use App\Core\loggers\LoggerFactory;

class Model {
    protected static $db;
    protected static $table;
    protected static $logger;

    protected static function init() {
        if (!self::$db) {
            $config = require __DIR__ . '/../../config.php';
            self::$db = Database::getInstance($config);

            if (!self::$logger) {
                self::$logger = LoggerFactory::create('file');
            }
            self::$logger->log('Connected to database successfully!');
        }
    }

    public static function all() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM " . static::$table);
        self::$logger->log('Fetch all ' . strtoupper(static::$table) . ' data successfully!');
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
        self::$logger->log('Fetch by id' . strtoupper(static::$table) . ' data successfully!');

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create(array $data) {
        self::init();
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = self::$db->prepare("INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        self::$logger->log('insert data to ' . strtoupper(static::$table) . '  successfully!');

        return self::$db->lastInsertId();
    }

    public static function where($column, $value) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM " . static::$table . " WHERE $column = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            self::$logger->log('Deleted ' . strtoupper(static::$table) . ' with ID ' . $id . ' successfully!');
        } else {
            self::$logger->log('Failed to delete ' . strtoupper(static::$table) . ' with ID ' . $id);
        }

        return $result;
    }
}
