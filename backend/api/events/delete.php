<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

$event_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($event_id)) {
    sendError("Event ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "DELETE FROM events WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $event_id);

if ($stmt->execute()) {
    sendSuccess("Event deleted successfully");
} else {
    sendError("Failed to delete event", null, 500);
}
?>
