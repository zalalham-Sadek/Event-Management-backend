<?php
namespace App\Core\loggers;

class DatabaseLogger implements Logger {
    protected static $db;

    // نهيئ الاتصال مرة واحدة
    protected static function init() {
        if (!self::$db) {
            $config = require __DIR__ . '/../../config.php';
            self::$db = Database::getInstance($config);

            self::$db->exec("
            CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        }
    }

    public function log(string $message): void {
        $stmt = $self::$db->prepare("INSERT INTO logs (message) VALUES (:message)");
        $stmt->execute(['message' => $message]);
    }
}
