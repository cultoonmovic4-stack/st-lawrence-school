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
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login.'
    ]);
    exit;
}

$currentUser = getCurrentUser();
$userId = $currentUser['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['event_title', 'event_date', 'start_time', 'end_time', 'location', 'category'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
            ]);
            exit;
        }
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Combine start_time and end_time into description or use start_time as event_time
    $eventTime = $data['start_time'];
    $description = $data['event_description'] ?? '';
    if (!empty($data['end_time'])) {
        $description .= "\nTime: " . $data['start_time'] . " - " . $data['end_time'];
    }
    
    // Insert event
    $stmt = $db->prepare("
        INSERT INTO events 
        (title, description, event_date, event_time, location, category, status, created_by, created_at) 
        VALUES 
        (:title, :description, :event_date, :event_time, :location, :category, 'upcoming', :created_by, NOW())
    ");
    
    $stmt->bindParam(':title', $data['event_title']);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':event_date', $data['event_date']);
    $stmt->bindParam(':event_time', $eventTime);
    $stmt->bindParam(':location', $data['location']);
    $stmt->bindParam(':category', $data['category']);
    $stmt->bindParam(':created_by', $userId);
    
    if ($stmt->execute()) {
        $event_id = $db->lastInsertId();
        
        // Log activity
        $log_stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, table_name, record_id, description, created_at)
            VALUES (:user_id, 'create', 'events', :record_id, :description, NOW())
        ");
        $log_stmt->bindParam(':user_id', $userId);
        $log_stmt->bindParam(':record_id', $event_id);
        $log_desc = "Created event: " . $data['event_title'];
        $log_stmt->bindParam(':description', $log_desc);
        $log_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Event created successfully',
            'event_id' => $event_id
        ]);
    } else {
        throw new Exception('Failed to create event');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
