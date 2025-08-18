<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected static $table = 'users';

    public static function findByEmail($email) {
        return self::where('email', $email);
    }

    public static function getRoles($userId) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT r.name 
            FROM roles r 
            JOIN user_roles ur ON ur.role_id = r.id 
            WHERE ur.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
