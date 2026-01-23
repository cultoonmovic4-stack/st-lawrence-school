<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get pending admissions count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admission_applications WHERE status = 'pending'");
    $stmt->execute();
    $pendingAdmissions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get unread messages count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'");
    $stmt->execute();
    $unreadMessages = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'admissions' => (int)$pendingAdmissions,
            'messages' => (int)$unreadMessages
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
