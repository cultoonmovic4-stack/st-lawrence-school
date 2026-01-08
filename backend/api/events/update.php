<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    sendError("Event ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$updates = array();
$params = array(":id" => $data->id);

if (isset($data->title)) {
    $updates[] = "title = :title";
    $params[":title"] = $data->title;
}
if (isset($data->description)) {
    $updates[] = "description = :description";
    $params[":description"] = $data->description;
}
if (isset($data->event_date)) {
    $updates[] = "event_date = :event_date";
    $params[":event_date"] = $data->event_date;
}
if (isset($data->event_time)) {
    $updates[] = "event_time = :event_time";
    $params[":event_time"] = $data->event_time;
}
if (isset($data->end_date)) {
    $updates[] = "end_date = :end_date";
    $params[":end_date"] = $data->end_date;
}
if (isset($data->location)) {
    $updates[] = "location = :location";
    $params[":location"] = $data->location;
}
if (isset($data->category)) {
    $updates[] = "category = :category";
    $params[":category"] = $data->category;
}
if (isset($data->image_url)) {
    $updates[] = "image_url = :image_url";
    $params[":image_url"] = $data->image_url;
}
if (isset($data->status)) {
    $updates[] = "status = :status";
    $params[":status"] = $data->status;
}

if (empty($updates)) {
    sendError("No fields to update", null, 400);
    exit();
}

$query = "UPDATE events SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = :id";
$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

if ($stmt->execute()) {
    sendSuccess("Event updated successfully");
} else {
    sendError("Failed to update event", null, 500);
}
?>
