<?php
namespace App\Models;

use App\Core\Model;
use App\Core\builder\QueryBuilder;
use PDO;

class Event extends Model {
    protected static $table = 'events';

    public static function findByTitle($title) {
        return self::where('title', $title);
    }

public static function findByLocationAndDate($location, $event_date) {
    self::init();

    // Make sure $event_date is in Y-m-d format (without time)
    $dateOnly = date('Y-m-d', strtotime($event_date));

    $stmt = self::$db->prepare("
        SELECT * 
        FROM events 
        WHERE location = :location 
          AND DATE(event_date) = :event_date
          AND deleted_at IS NULL
        LIMIT 1
    ");
    $stmt->execute([
        'location' => $location,
        'event_date' => $dateOnly
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}