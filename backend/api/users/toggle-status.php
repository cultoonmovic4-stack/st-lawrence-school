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
requirePermission('user.edit');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    $isActive = $data['is_active'] ?? null;
    
    if (empty($userId) || $isActive === null) {
        throw new Exception('User ID and status are required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Prevent deactivating yourself
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId && $isActive == 0) {
        throw new Exception('You cannot deactivate your own account');
    }
    
    // Convert is_active (0/1) to status (active/inactive)
    $status = $isActive == 1 ? 'active' : 'inactive';
    
    // Update user status
    $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :user_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':user_id', $userId);
    
    if ($stmt->execute()) {
        $action = $isActive == 1 ? 'activated' : 'deactivated';
        echo json_encode([
            'success' => true,
            'message' => "User {$action} successfully!"
        ]);
    } else {
        throw new Exception('Failed to update user status');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
