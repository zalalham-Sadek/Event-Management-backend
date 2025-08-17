<?php
namespace App\Migrations;

use PDO;

class CreateRolesTable {
    public function up(PDO $db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ");
    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS roles");
    }
}
