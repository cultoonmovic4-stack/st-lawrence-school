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
        throw new Exception('Image ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get image info first
    $stmt = $db->prepare("SELECT image_url FROM gallery_images WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$image) {
        throw new Exception('Image not found');
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM gallery_images WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    
    if ($stmt->execute()) {
        // Try to delete physical file
        $filePath = __DIR__ . '/../../' . $image['image_url'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete image');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
