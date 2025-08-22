<?php
namespace App\Migrations;

use PDO;

class CreateUserRolesTable {
    public function up(PDO $db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS user_roles (
                user_id INT NOT NULL,
                role_id INT NOT NULL,
                PRIMARY KEY(user_id, role_id),
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
            );
        ");
    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS user_roles");
    }
}
