<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

$event_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($event_id)) {
    sendError("Event ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM events WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $event_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    sendSuccess("Event retrieved successfully", $event);
} else {
    sendError("Event not found", null, 404);
}
?>
