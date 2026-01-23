<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check which password column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $passwordColumn = in_array('password', $columns) ? 'password' : 'password_hash';
    
    // Check if profile_image column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    $hasProfileImage = $stmt->rowCount() > 0;
    
    // Build query based on available columns
    $selectFields = "id, username, email, full_name, phone, created_at";
    if ($hasProfileImage) {
        $selectFields .= ", profile_image";
    }
    
    $stmt = $db->prepare("SELECT {$selectFields} FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Add profile_image as null if column doesn't exist
        if (!$hasProfileImage) {
            $user['profile_image'] = null;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    } else {
        throw new Exception('User not found');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
