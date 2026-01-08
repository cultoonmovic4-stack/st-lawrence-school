<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
        'message' => 'You do not have permission to assign permissions'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['role_id']) || !isset($data['permission_ids'])) {
        throw new Exception('Role ID and permission IDs are required');
    }
    
    $role_id = intval($data['role_id']);
    $permission_ids = $data['permission_ids'];
    $user_id = $auth['user']['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();
    
    // Remove existing permissions for this role
    $delete_query = "DELETE FROM role_permissions WHERE role_id = :role_id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->bindParam(':role_id', $role_id);
    $delete_stmt->execute();
    
    // Insert new permissions
    if (!empty($permission_ids)) {
        $insert_query = "INSERT INTO role_permissions (role_id, permission_id, granted_by) 
                        VALUES (:role_id, :permission_id, :granted_by)";
        $insert_stmt = $db->prepare($insert_query);
        
        foreach ($permission_ids as $permission_id) {
            $insert_stmt->bindParam(':role_id', $role_id);
            $insert_stmt->bindParam(':permission_id', $permission_id);
            $insert_stmt->bindParam(':granted_by', $user_id);
            $insert_stmt->execute();
        }
    }
    
    // Log activity
    logActivity($db, $user_id, 'assign_permissions', 'Roles', "Assigned permissions to role ID: $role_id");
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Permissions assigned successfully'
    ]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error assigning permissions: ' . $e->getMessage()
    ]);
}
?>
