<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    // Get upcoming events from calendar_events table
    $stmt = $db->prepare("
        SELECT 
            id,
            event_title,
            event_description,
            start_date,
            end_date,
            event_type,
            all_day,
            color
        FROM calendar_events
        WHERE start_date >= CURDATE()
        ORDER BY start_date ASC, id ASC
        LIMIT :limit
    ");
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format events for display
    $formattedEvents = [];
    foreach ($events as $event) {
        $startDate = new DateTime($event['start_date']);
        $endDate = $event['end_date'] ? new DateTime($event['end_date']) : null;
        
        // Format time display
        $timeDisplay = '';
        if ($event['all_day']) {
            $timeDisplay = 'All Day';
        } else {
            // If you have time fields, use them. For now, showing as all day
            $timeDisplay = 'All Day';
        }
        
        $formattedEvents[] = [
            'id' => $event['id'],
            'title' => $event['event_title'],
            'description' => $event['event_description'],
            'date' => $event['start_date'],
            'day' => $startDate->format('d'),
            'month' => strtoupper($startDate->format('M')),
            'time' => $timeDisplay,
            'type' => $event['event_type'],
            'color' => $event['color'] ?: '#0066cc'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formattedEvents
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
