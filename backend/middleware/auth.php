<?php
// Authentication and Authorization Middleware

function authenticate() {
    // Check if user is logged in via session
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        return [
            'success' => false,
            'message' => 'Authentication required'
        ];
    }
    
    try {
        require_once __DIR__ . '/../api/config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT u.*, r.role_name, r.role_display_name, r.level 
                  FROM users u
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE u.id = :user_id AND u.status = 'active'";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found or inactive'
            ];
        }
        
        return [
            'success' => true,
            'user' => $user
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Authentication error: ' . $e->getMessage()
        ];
    }
}

function hasPermission($user_id, $permission_name) {
    try {
        require_once __DIR__ . '/../api/config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if user has super_admin role (has all permissions)
        $role_query = "SELECT r.role_name 
                      FROM users u
                      JOIN roles r ON u.role_id = r.id
                      WHERE u.id = :user_id";
        
        $role_stmt = $db->prepare($role_query);
        $role_stmt->bindParam(':user_id', $user_id);
        $role_stmt->execute();
        $role = $role_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($role && $role['role_name'] === 'super_admin') {
            return true;
        }
        
        // Check specific permission
        $query = "SELECT COUNT(*) as has_permission
                  FROM users u
                  JOIN role_permissions rp ON u.role_id = rp.role_id
                  JOIN permissions p ON rp.permission_id = p.id
                  WHERE u.id = :user_id 
                  AND p.permission_name = :permission_name";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':permission_name', $permission_name);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['has_permission'] > 0;
        
    } catch (Exception $e) {
        return false;
    }
}

function getUserPermissions($user_id) {
    try {
        require_once __DIR__ . '/../api/config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT p.permission_name, p.permission_display_name, p.module
                  FROM users u
                  JOIN role_permissions rp ON u.role_id = rp.role_id
                  JOIN permissions p ON rp.permission_id = p.id
                  WHERE u.id = :user_id
                  ORDER BY p.module, p.permission_display_name";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        return [];
    }
}

function logActivity($db, $user_id, $action, $module, $description) {
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $query = "INSERT INTO activity_logs (user_id, action, module, description, ip_address, user_agent)
                  VALUES (:user_id, :action, :module, :description, :ip_address, :user_agent)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':module', $module);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
