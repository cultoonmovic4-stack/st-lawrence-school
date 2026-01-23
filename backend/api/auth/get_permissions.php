<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';
require_once '../middleware/permission_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $permissions = getUserPermissions();
    $accessibleModules = getAccessibleModules();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'permissions' => $permissions,
            'accessible_modules' => $accessibleModules,
            'role_name' => $_SESSION['role_name'] ?? 'Unknown',
            'role_level' => $_SESSION['role_level'] ?? 0,
            'is_super_admin' => ($_SESSION['role_level'] ?? 0) >= 100
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
