<?php
/**
 * Database Setup Script for Roles and Permissions
 * Run this file once to create all necessary tables
 */

require_once '../api/config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Roles & Permissions</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e4d9f;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step h3 {
            margin-top: 0;
            color: #495057;
        }
        ul {
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1e4d9f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #163a7a;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔐 Roles & Permissions Setup</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<div class='info'><strong>Starting database setup...</strong></div>";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/roles_permissions.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    echo "<div class='step'><h3>Executing SQL Statements...</h3>";
    
    foreach ($statements as $statement) {
        try {
            $db->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            // Ignore duplicate key errors and already exists errors
            if (strpos($e->getMessage(), 'Duplicate') === false && 
                strpos($e->getMessage(), 'already exists') === false) {
                echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
                $errorCount++;
            }
        }
    }
    
    echo "</div>";
    
    // Verify tables were created
    echo "<div class='step'><h3>Verifying Tables...</h3>";
    
    $tables = ['roles', 'permissions', 'role_permissions', 'activity_logs'];
    $allTablesExist = true;
    
    foreach ($tables as $table) {
        $query = "SHOW TABLES LIKE '$table'";
        $stmt = $db->query($query);
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✓ Table '$table' created successfully</div>";
        } else {
            echo "<div class='error'>✗ Table '$table' not found</div>";
            $allTablesExist = false;
        }
    }
    
    echo "</div>";
    
    // Count records
    if ($allTablesExist) {
        echo "<div class='step'><h3>Database Statistics:</h3>";
        
        $rolesCount = $db->query("SELECT COUNT(*) FROM roles")->fetchColumn();
        $permissionsCount = $db->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
        $rolePermissionsCount = $db->query("SELECT COUNT(*) FROM role_permissions")->fetchColumn();
        
        echo "<ul>
            <li><strong>Roles:</strong> $rolesCount</li>
            <li><strong>Permissions:</strong> $permissionsCount</li>
            <li><strong>Role-Permission Assignments:</strong> $rolePermissionsCount</li>
        </ul>";
        
        echo "</div>";
        
        // List roles
        echo "<div class='step'><h3>Created Roles:</h3><ul>";
        $rolesQuery = $db->query("SELECT role_display_name, level FROM roles ORDER BY level DESC");
        while ($role = $rolesQuery->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>{$role['role_display_name']} (Level {$role['level']})</li>";
        }
        echo "</ul></div>";
    }
    
    // Check if users table has role_id column
    echo "<div class='step'><h3>Checking Users Table...</h3>";
    try {
        $checkColumn = $db->query("SHOW COLUMNS FROM users LIKE 'role_id'");
        if ($checkColumn->rowCount() > 0) {
            echo "<div class='success'>✓ 'role_id' column added to users table</div>";
        } else {
            echo "<div class='error'>✗ 'role_id' column not found in users table</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>Error checking users table: " . $e->getMessage() . "</div>";
    }
    echo "</div>";
    
    // Final summary
    echo "<div class='step'>";
    if ($allTablesExist && $errorCount == 0) {
        echo "<div class='success'>
            <h3>✅ Setup Completed Successfully!</h3>
            <p>All tables have been created and populated with default data.</p>
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Assign roles to existing users in the database</li>
                <li>Access the Roles Management page to customize permissions</li>
                <li>Test the system with different user roles</li>
            </ul>
        </div>";
        echo "<a href='../admin/roles.html' class='btn'>Go to Roles Management</a>";
    } else {
        echo "<div class='error'>
            <h3>⚠️ Setup Completed with Warnings</h3>
            <p>Some errors occurred during setup. Please review the messages above.</p>
        </div>";
    }
    echo "</div>";
    
    echo "<div class='info'>
        <strong>Important:</strong> For security reasons, delete or rename this setup file after successful installation.
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h3>❌ Setup Failed</h3>
        <p><strong>Error:</strong> " . $e->getMessage() . "</p>
        <p>Please check your database configuration and try again.</p>
    </div>";
}

echo "</div></body></html>";
?>
