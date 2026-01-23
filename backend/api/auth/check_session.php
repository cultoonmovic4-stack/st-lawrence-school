<?php
/**
 * Check Session API Endpoint
 * Verifies if user has an active session
 */

// Set headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

// Start session
session_start();

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'data' => [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'role_name' => $_SESSION['role_name'] ?? null,
            'role_level' => $_SESSION['role_level'] ?? null
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'message' => 'No active session'
    ]);
}
?>
