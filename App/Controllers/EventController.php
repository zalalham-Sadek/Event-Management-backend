<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\EventSpeaker;

class EventController extends Controller {
    // Web routes
    public function index() {
        $events = Event::all();
        $this->view('event_list', compact('events'));
    }

    public function create() {
        // Implementation for web route
    }

    public function edit($id) {
        $event = Event::find($id);
        $this->view('edit_event', compact('event'));
    }

    // API routes
    public function getAllEvents() {
        $events = Event::all();
        return $this->json(['events' => $events]);
    }

    public function getEvent($id) {
        $event = Event::find($id);
        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }
        return $this->json(['event' => $event]);
    }

public function createEvent() {
    // Get JSON data from request
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['title']) || !isset($data['event_date']) || !isset($data['location'])) {
        return $this->json(['error' => 'Missing required fields'], 400);
    }

    // Check if title already exists
    if (Event::findByTitle($data['title'])) {
        return $this->json(['error' => 'Event title already exists'], 409);
    }

    // Check for conflicting event at the same location and date
    $conflictEvent = Event::findByLocationAndDate($data['location'], $data['event_date']);
    if ($conflictEvent) {
        return $this->json([
            'error' => 'There is already an event scheduled at this location and date',
            'conflict_event' => $conflictEvent
        ], 409);
    }

    try {
        $eventId = Event::create([
            "title" => $data["title"],
            "type" => $data["type"] ?? null,
            "event_date" => $data["event_date"],
            "audience" => $data["audience"] ?? "general",
            "location" => $data["location"],
            "description" => $data["description"] ?? null,
            "duration_minutes" => $data["duration_minutes"] ?? 2
        ]);

        if (isset($data['speakers']) && is_array($data['speakers'])) {
            foreach ($data['speakers'] as $speakerId) {
                EventSpeaker::create([
                    'event_id' => $eventId,
                    'speaker_id' => $speakerId
                ]);
            }
        }

        $event = Event::find($eventId);


        return $this->json(['message' => 'Event created successfully', 'event' => $event], 201);
    } catch (\Exception $e) {
        return $this->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
    }
}


function deleteEvent($id) {
    $event = Event::find($id);
    if (!$event) {
        return $this->json(['error' => 'Event not found'], 404);
    }

    try {
        Event::delete($id);
        return $this->json(['message' => 'Event deleted successfully']);
    } catch (\Exception $e) {
        return $this->json(['error' => 'Failed to delete event: ' . $e->getMessage()], 500);
    }
}

}
