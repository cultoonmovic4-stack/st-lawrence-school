<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
$auth = check_auth();
if (!$auth['authenticated']) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    // Get upcoming events
    $stmt = $db->prepare("
        SELECT 
            event_id,
            event_title,
            event_description,
            event_date,
            start_time,
            end_time,
            location,
            status
        FROM events
        WHERE event_date >= CURDATE()
        AND status = 'active'
        ORDER BY event_date ASC, start_time ASC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format events
    $formattedEvents = array_map(function($event) {
        $eventDate = new DateTime($event['event_date']);
        
        return [
            'id' => $event['event_id'],
            'title' => $event['event_title'],
            'description' => $event['event_description'],
            'date' => $event['event_date'],
            'day' => $eventDate->format('d'),
            'month' => strtoupper($eventDate->format('M')),
            'start_time' => date('g:i A', strtotime($event['start_time'])),
            'end_time' => date('g:i A', strtotime($event['end_time'])),
            'time_range' => date('g:i A', strtotime($event['start_time'])) . ' - ' . date('g:i A', strtotime($event['end_time'])),
            'location' => $event['location'],
            'status' => $event['status']
        ];
    }, $events);
    
    echo json_encode([
        'success' => true,
        'data' => $formattedEvents
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
