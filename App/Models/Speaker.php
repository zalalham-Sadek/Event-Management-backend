<?php
namespace App\Models;

use App\Core\Model;
use App\Core\builder\QueryBuilder;
use PDO;

class Speaker extends Model {
    protected static $table = 'speakers';

    // Find speaker by email
    public static function findByEmail($email) {
        return self::where('email', $email);
    }

    // Get all events assigned to a speaker
    public static function getEventsBySpeaker($speakerId) {
        self::init();
        $stmt = self::$db->prepare("
            SELECT e.* 
            FROM events e 
            JOIN event_speakers es ON es.event_id = e.id 
            WHERE es.speaker_id = :speaker_id
        ");
        $stmt->execute(['speaker_id' => $speakerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if a speaker is already assigned to an event
    public static function isAssignedToEvent($speakerId, $eventId) {
        self::init();
        $query = QueryBuilder::table("event_speakers")
            ->select("event_speakers.id")
            ->where("event_speakers.speaker_id", "=", $speakerId)
            ->where("event_speakers.event_id", "=", $eventId)
            ->limit(1);

        $stmt = self::$db->prepare($query->toSql());
        $stmt->execute($query->getBindings());
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $assignment ?: false;
    }
}
