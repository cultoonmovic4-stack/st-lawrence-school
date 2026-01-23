<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';
require_once '../middleware/permission_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check permission
requirePermission('user.delete');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    
    if (empty($userId)) {
        throw new Exception('User ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Prevent deleting yourself
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
        throw new Exception('You cannot delete your own account');
    }
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('User not found');
    }
    
    // Delete user
    $stmt = $db->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    } else {
        throw new Exception('Failed to delete user');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
