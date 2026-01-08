<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

// Verify token and require admin
$user = verifyToken();
requireAdmin($user);

// Get teacher ID from URL
$teacher_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($teacher_id)) {
    sendError("Teacher ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if teacher exists
$check_query = "SELECT id, name, photo_url FROM teachers WHERE id = :id";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":id", $teacher_id);
$check_stmt->execute();

if ($check_stmt->rowCount() == 0) {
    sendError("Teacher not found", null, 404);
    exit();
}

$teacher = $check_stmt->fetch(PDO::FETCH_ASSOC);

// Delete teacher
$query = "DELETE FROM teachers WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $teacher_id);

if ($stmt->execute()) {
    // Delete photo file if exists
    if (!empty($teacher['photo_url']) && file_exists("../../" . $teacher['photo_url'])) {
        unlink("../../" . $teacher['photo_url']);
    }
    
    // Log activity
    $log_query = "INSERT INTO admin_activity_logs (user_id, action_type, table_name, record_id, description, ip_address) 
                  VALUES (:user_id, 'DELETE', 'teachers', :record_id, :description, :ip)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":user_id", $user->id);
    $log_stmt->bindParam(":record_id", $teacher_id);
    $description = "Deleted teacher: " . $teacher['name'];
    $log_stmt->bindParam(":description", $description);
    $log_stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
    $log_stmt->execute();
    
    sendSuccess("Teacher deleted successfully");
} else {
    sendError("Failed to delete teacher", null, 500);
}
?>
