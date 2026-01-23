<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get next 3 upcoming events for hero section
    $stmt = $db->prepare("
        SELECT 
            title as event_title,
            event_date,
            location
        FROM events
        WHERE event_date >= CURDATE()
        AND status = 'upcoming'
        ORDER BY event_date ASC
        LIMIT 3
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $events
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
