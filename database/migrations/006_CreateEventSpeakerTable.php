<?php

namespace App\Migrations;
use PDO;

class CreateEventSpeakerTable {

    public function up(PDO $db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS event_speaker (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                speaker_id INT NOT NULL,
                role VARCHAR(100) DEFAULT 'Speaker',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                FOREIGN KEY (speaker_id) REFERENCES speakers(id) ON DELETE CASCADE,
                UNIQUE(event_id, speaker_id)
            );
        ");
    }

    public function down(PDO $db) {
        $db->exec("DROP TABLE IF EXISTS event_speaker");
    }
}