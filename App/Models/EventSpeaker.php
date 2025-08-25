<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class EventSpeaker extends Model
{
    protected static $table = 'event_speaker';

    public static function findSpeakersByEventId($event_id)
    {
        self::init();
        $stmt = self::$db->prepare("SELECT s.* FROM speakers s
                                        JOIN event_speaker es ON s.id = es.speaker_id
                                        WHERE es.event_id = :event_id");
        $stmt->execute(['event_id' => $event_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}

