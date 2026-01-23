<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate required fields
    if (empty($data['event_title']) || empty($data['start_date']) || empty($data['event_type'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: event_title, start_date, or event_type'
        ]);
        exit;
    }
    
    $stmt = $db->prepare("
        INSERT INTO calendar_events (
            event_title,
            event_description,
            start_date,
            end_date,
            event_type,
            all_day,
            color,
            created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $end_date = !empty($data['end_date']) ? $data['end_date'] : $data['start_date'];
    $all_day = isset($data['all_day']) ? (bool)$data['all_day'] : true;
    $color = !empty($data['color']) ? $data['color'] : '#0066cc';
    $created_by = $_SESSION['user_id'] ?? null;
    
    $stmt->execute([
        $data['event_title'],
        $data['event_description'] ?? '',
        $data['start_date'],
        $end_date,
        $data['event_type'],
        $all_day,
        $color,
        $created_by
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Calendar event created successfully',
        'id' => $db->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
