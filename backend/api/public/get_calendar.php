<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get important days (holidays and activities)
    $stmt = $db->prepare("
        SELECT 
            event_title,
            start_date as event_date,
            event_description,
            event_type,
            color
        FROM calendar_events
        WHERE event_type IN ('holiday', 'activity', 'other')
        AND start_date >= CURDATE()
        ORDER BY start_date ASC
    ");
    $stmt->execute();
    $important_days = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get academic calendar (exams and meetings)
    $stmt = $db->prepare("
        SELECT 
            event_title,
            start_date as event_date,
            event_description,
            event_type,
            color
        FROM calendar_events
        WHERE event_type IN ('exam', 'meeting')
        AND start_date >= CURDATE()
        ORDER BY start_date ASC
    ");
    $stmt->execute();
    $academic_calendar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all upcoming events for calendar display
    $stmt = $db->prepare("
        SELECT 
            event_title,
            start_date as event_date,
            event_description,
            event_type,
            color
        FROM calendar_events
        WHERE start_date >= CURDATE()
        ORDER BY start_date ASC
        LIMIT 10
    ");
    $stmt->execute();
    $all_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'important_days' => $important_days,
            'academic_calendar' => $academic_calendar,
            'all_events' => $all_events
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

