<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../api/config/database.php';
require_once '../middleware/auth.php';

// Check authentication
$auth = authenticate();
if (!$auth['success']) {
    http_response_code(401);
    echo json_encode($auth);
    exit;
}

// Check permission
if (!hasPermission($auth['user']['id'], 'settings.manage_roles')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to view roles'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT 
                r.id,
                r.role_name,
                r.role_display_name,
                r.description,
                r.level,
                r.is_active,
                r.created_at,
                COUNT(DISTINCT u.id) as user_count,
                COUNT(DISTINCT rp.permission_id) as permission_count
              FROM roles r
              LEFT JOIN users u ON r.id = u.role_id
              LEFT JOIN role_permissions rp ON r.id = rp.role_id
              GROUP BY r.id
              ORDER BY r.level DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $roles
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching roles: ' . $e->getMessage()
    ]);
}
?>
