<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Speaker;

class SpeakerController extends Controller {

    // ✅ Get all speakers
    public function getAllSpeakers() {
        $speakers = Speaker::all();
        return $this->json(['speakers' => $speakers]);
    }

    // ✅ Get a single speaker by ID
    public function getSpeaker($id) {
        $speaker = Speaker::find($id);
        if (!$speaker) {
            return $this->json(['error' => 'Speaker not found'], 404);
        }
        return $this->json(['speaker' => $speaker]);
    }

    // ✅ Create a new speaker
    public function createSpeaker() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['name']) || !isset($data['bio'])) {
            return $this->json(['error' => 'Missing required fields: name, bio'], 400);
        }

        try {
            // 🔹 Check if email exists (only if email is provided)
            if (!empty($data['email'])) {
                $existing = Speaker::findByEmail($data['email']);
                if ($existing) {
                    return $this->json(['error' => 'Email already exists for another speaker'], 409);
                }
            }

            // 🔹 Create new speaker
            $speakerId = Speaker::create([
                "name" => $data["name"],
                "bio" => $data["bio"],
                "email" => $data["email"] ?? null,
                "phone" => $data["phone"] ?? null,
                "availabe_date" => $data["availabe_date"] ?? null
            ]);

            $speaker = Speaker::find($speakerId);
            return $this->json(['message' => 'Speaker created successfully', 'speaker' => $speaker], 201);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to create speaker: ' . $e->getMessage()], 500);
        }
    }


    // ✅ Update an existing speaker
    public function updateSpeaker($id) {
        $speaker = Speaker::find($id);
        if (!$speaker) {
            return $this->json(['error' => 'Speaker not found'], 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            Speaker::update($id, [
                "name" => $data["name"] ?? $speaker["name"],
                "bio" => $data["bio"] ?? $speaker["bio"],
                "email" => $data["email"] ?? $speaker["email"],
                "phone" => $data["phone"] ?? $speaker["phone"],
                "organization" => $data["organization"] ?? $speaker["organization"]
            ]);

            $updatedSpeaker = Speaker::find($id);
            return $this->json(['message' => 'Speaker updated successfully', 'speaker' => $updatedSpeaker]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to update speaker: ' . $e->getMessage()], 500);
        }
    }

    // ✅ Delete a speaker
    public function deleteSpeaker($id) {
        $speaker = Speaker::find($id);
        if (!$speaker) {
            return $this->json(['error' => 'Speaker not found'], 404);
        }

        try {
            Speaker::delete($id);
            return $this->json(['message' => 'Speaker deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to delete speaker: ' . $e->getMessage()], 500);
        }
    }
}
