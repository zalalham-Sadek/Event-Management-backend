<?php
class CreateUsersTable {
    public function up(PDO $db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS users");
    }
}
