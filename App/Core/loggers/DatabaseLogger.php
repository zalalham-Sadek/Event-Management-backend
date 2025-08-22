<?php
namespace App\Core\loggers;

use App\Core\Database;

class DatabaseLogger implements Logger {
    protected static $db;

    // نهيئ الاتصال مرة واحدة
    protected static function init() {
        if (!self::$db) {
            $config = require __DIR__ . '/../../../config.php';
            self::$db = Database::getInstance($config);

            self::$db->exec("
            CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                level VARCHAR(10) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        }
    }

    public function log(string $message, string $level = 'INFO'): void {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO logs (level, message) VALUES (:level, :message)");
        $stmt->execute([
            'level' => $level,
            'message' => $message
        ]);
    }
    
    public function info(string $message) {
        $this->log($message, 'INFO');
    }

    public function warning(string $message) {
        $this->log($message, 'WARNING');
    }

    public function error(string $message) {
        $this->log($message, 'ERROR');
    }
}
