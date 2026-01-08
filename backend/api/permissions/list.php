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
if (!hasPermission($auth['user']['id'], 'settings.manage_permissions')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to view permissions'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get role_id if specified
    $role_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : null;
    
    if ($role_id) {
        // Get permissions for specific role
        $query = "SELECT 
                    p.id,
                    p.permission_name,
                    p.permission_display_name,
                    p.module,
                    p.description,
                    IF(rp.id IS NOT NULL, 1, 0) as is_granted
                  FROM permissions p
                  LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role_id = :role_id
                  ORDER BY p.module, p.permission_display_name";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':role_id', $role_id);
    } else {
        // Get all permissions grouped by module
        $query = "SELECT 
                    id,
                    permission_name,
                    permission_display_name,
                    module,
                    description
                  FROM permissions
                  ORDER BY module, permission_display_name";
        
        $stmt = $db->prepare($query);
    }
    
    $stmt->execute();
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by module
    $grouped = [];
    foreach ($permissions as $permission) {
        $module = $permission['module'];
        if (!isset($grouped[$module])) {
            $grouped[$module] = [];
        }
        $grouped[$module][] = $permission;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $permissions,
        'grouped' => $grouped
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching permissions: ' . $e->getMessage()
    ]);
}
?>
