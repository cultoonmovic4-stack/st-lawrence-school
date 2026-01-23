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
        throw new Exception('Resource ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get resource info first
    $stmt = $db->prepare("SELECT file_url FROM library_resources WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resource) {
        throw new Exception('Resource not found');
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM library_resources WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    
    if ($stmt->execute()) {
        // Try to delete physical file
        if ($resource['file_url']) {
            $filePath = __DIR__ . '/../../' . $resource['file_url'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Resource deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete resource');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
