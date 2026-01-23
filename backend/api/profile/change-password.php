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
    $data = json_decode(file_get_contents('php://input'), true);
    
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('All fields are required');
    }
    
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New passwords do not match');
    }
    
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check which password column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $passwordColumn = in_array('password', $columns) ? 'password' : 'password_hash';
    
    // Verify current password
    $stmt = $db->prepare("SELECT {$passwordColumn} as password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        throw new Exception('Current password is incorrect');
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET {$passwordColumn} = :password WHERE id = :id");
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':id', $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    } else {
        throw new Exception('Failed to change password');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
