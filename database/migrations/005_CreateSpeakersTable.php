<?php
namespace App\Migrations;

use PDO;

class CreateSpeakersTable {

    public function up(PDO $db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS speakers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(150) UNIQUE,
                phone VARCHAR(20),
                bio TEXT,
                availabe_date DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL
            );
        ");
    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS speakers");
    }
}