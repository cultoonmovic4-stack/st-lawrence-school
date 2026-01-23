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
requirePermission('user.create');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check which password column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password%'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $passwordColumn = in_array('password', $columns) ? 'password' : 'password_hash';
    
    // Get form data
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $roleId = $_POST['role_id'] ?? null;
    
    // Validate required fields
    if (empty($username) || empty($email) || empty($password)) {
        throw new Exception('Username, email, and password are required');
    }
    
    if (empty($roleId)) {
        throw new Exception('Role is required');
    }
    
    // Check if username or email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Username or email already exists');
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (username, email, {$passwordColumn}, full_name, phone, role_id, status, created_at) 
            VALUES (:username, :email, :password, :full_name, :phone, :role_id, 'active', NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':full_name', $fullName);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':role_id', $roleId);
    
    if ($stmt->execute()) {
        // Log activity
        $userId = $db->lastInsertId();
        $logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (:user_id, 'user_created', :description, :ip)");
        $currentUserId = $_SESSION['user_id'];
        $description = "Created new user: {$username}";
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bindParam(':user_id', $currentUserId);
        $logStmt->bindParam(':description', $description);
        $logStmt->bindParam(':ip', $ip);
        $logStmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'User created successfully!',
            'user_id' => $userId
        ]);
    } else {
        throw new Exception('Failed to create user');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
