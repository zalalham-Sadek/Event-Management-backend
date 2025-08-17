<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected static $table = 'users'; // ممكن تحدد أو تخليه يحدد تلقائي

    // تقدر تضيف دوال خاصة بهذا الموديل
    public static function activeUsers() {
        $stmt = self::$db->query("SELECT * FROM users WHERE status = 'active'");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
