<?php
/**
 * Logout API Endpoint
 * Handles user logout and session destruction
 */

// Set headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No active session found'
    ]);
    exit();
}

try {
    // Include database connection
    require_once '../config/Database.php';
    
    // Log logout activity
    if (isset($_SESSION['user_id'])) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $logQuery = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                     VALUES (:user_id, 'logout', 'User logged out', :ip, :user_agent)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bindParam(':user_id', $_SESSION['user_id']);
        $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $logStmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $logStmt->execute();
    }
    
    // Destroy session
    session_unset();
    session_destroy();
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
    
} catch (Exception $e) {
    // Even if logging fails, still logout
    session_unset();
    session_destroy();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
}
?>
