<?php
namespace App\Migrations;

use PDO;

class CreateEventsTable {

    public function up(PDO $db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            type VARCHAR(100) NULL,
            audience VARCHAR(255) NULL,
            location VARCHAR(255),
            event_date DATETIME NOT NULL,
            duration_minutes INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL
        );
    ");

    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS events");
    }
}