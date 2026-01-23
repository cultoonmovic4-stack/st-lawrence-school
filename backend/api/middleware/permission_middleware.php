<?php
/**
 * Permission Middleware
 * Handles role-based access control and permission checking
 */

// Check if user has specific permission
function hasPermission($permissionName) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
        return false;
    }
    
    // Super Administrator has all permissions
    if ($_SESSION['role_level'] >= 100) {
        return true;
    }
    
    // Check if user's role has the permission
    try {
        require_once __DIR__ . '/../config/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as has_permission
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id AND p.permission_name = :permission_name
        ");
        
        $stmt->bindParam(':role_id', $_SESSION['role_id']);
        $stmt->bindParam(':permission_name', $permissionName);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['has_permission'] > 0;
        
    } catch (Exception $e) {
        error_log("Permission check error: " . $e->getMessage());
        return false;
    }
}

// Check if user has any of the specified permissions
function hasAnyPermission($permissions) {
    foreach ($permissions as $permission) {
        if (hasPermission($permission)) {
            return true;
        }
    }
    return false;
}

// Check if user has all specified permissions
function hasAllPermissions($permissions) {
    foreach ($permissions as $permission) {
        if (!hasPermission($permission)) {
            return false;
        }
    }
    return true;
}

// Check if user has minimum role level
function hasMinimumRoleLevel($minLevel) {
    if (!isset($_SESSION['role_level'])) {
        return false;
    }
    return $_SESSION['role_level'] >= $minLevel;
}

// Require specific permission or return error
function requirePermission($permissionName) {
    if (!hasPermission($permissionName)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. You do not have permission to perform this action.',
            'required_permission' => $permissionName
        ]);
        exit;
    }
}

// Require minimum role level or return error
function requireMinimumRoleLevel($minLevel) {
    if (!hasMinimumRoleLevel($minLevel)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. Insufficient privileges.',
            'required_level' => $minLevel
        ]);
        exit;
    }
}

// Get user's permissions
function getUserPermissions() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
        return [];
    }
    
    // Super Administrator has all permissions
    if ($_SESSION['role_level'] >= 100) {
        return ['*']; // Wildcard for all permissions
    }
    
    try {
        require_once __DIR__ . '/../config/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("
            SELECT p.permission_name, p.permission_module
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
            ORDER BY p.permission_module, p.permission_name
        ");
        
        $stmt->bindParam(':role_id', $_SESSION['role_id']);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Get permissions error: " . $e->getMessage());
        return [];
    }
}

// Check module access
function canAccessModule($moduleName) {
    if (!isset($_SESSION['role_level'])) {
        return false;
    }
    
    // Super Administrator can access all modules
    if ($_SESSION['role_level'] >= 100) {
        return true;
    }
    
    // Define module access by role level
    $moduleAccess = [
        'user_management' => 80,      // Administrator and above
        'student_management' => 50,   // Teacher and above
        'staff_management' => 80,     // Administrator and above
        'admissions' => 30,           // Receptionist and above
        'finance' => 60,              // Accountant and above
        'academics' => 50,            // Teacher and above
        'library' => 40,              // Librarian and above
        'content' => 80,              // Administrator and above
        'communications' => 30,       // Receptionist and above
        'reports' => 50,              // Teacher and above
        'settings' => 100,            // Super Administrator only
        'dashboard' => 30             // Receptionist and above
    ];
    
    $requiredLevel = $moduleAccess[$moduleName] ?? 100;
    return $_SESSION['role_level'] >= $requiredLevel;
}

// Get accessible modules for current user
function getAccessibleModules() {
    $allModules = [
        'dashboard',
        'user_management',
        'student_management',
        'staff_management',
        'admissions',
        'finance',
        'academics',
        'library',
        'content',
        'communications',
        'reports',
        'settings'
    ];
    
    $accessible = [];
    foreach ($allModules as $module) {
        if (canAccessModule($module)) {
            $accessible[] = $module;
        }
    }
    
    return $accessible;
}
