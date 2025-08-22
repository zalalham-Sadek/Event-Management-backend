<?php
namespace App\Models;

use App\Core\Model;

class Role extends Model {
    protected static $table = 'roles';

    public static function findByName($name) {
        return self::where('name', $name);
    }
}
