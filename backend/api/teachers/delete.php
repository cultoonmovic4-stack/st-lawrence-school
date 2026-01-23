<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['id'])) {
        throw new Exception('Teacher ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get teacher info first
    $stmt = $db->prepare("SELECT photo_url FROM teachers WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher) {
        throw new Exception('Teacher not found');
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM teachers WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    
    if ($stmt->execute()) {
        // Try to delete physical file
        if ($teacher['photo_url']) {
            $filePath = __DIR__ . '/../../' . $teacher['photo_url'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete teacher');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
