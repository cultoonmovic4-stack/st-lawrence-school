<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

$resource_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($resource_id)) {
    sendError("Resource ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$check_query = "SELECT file_path FROM library_resources WHERE id = :id";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":id", $resource_id);
$check_stmt->execute();

if ($check_stmt->rowCount() == 0) {
    sendError("Resource not found", null, 404);
    exit();
}

$resource = $check_stmt->fetch(PDO::FETCH_ASSOC);

$query = "DELETE FROM library_resources WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $resource_id);

if ($stmt->execute()) {
    if (!empty($resource['file_path']) && file_exists("../../" . $resource['file_path'])) {
        unlink("../../" . $resource['file_path']);
    }
    
    sendSuccess("Resource deleted successfully");
} else {
    sendError("Failed to delete resource", null, 500);
}
?>
