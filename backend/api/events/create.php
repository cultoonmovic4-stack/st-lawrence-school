<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->title) || empty($data->event_date)) {
    sendError("Title and event date are required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "INSERT INTO events 
          (title, description, event_date, event_time, end_date, location, category, image_url, status, created_by) 
          VALUES 
          (:title, :description, :event_date, :event_time, :end_date, :location, :category, :image_url, :status, :created_by)";

$stmt = $db->prepare($query);

$stmt->bindParam(":title", $data->title);
$description = isset($data->description) ? $data->description : null;
$stmt->bindParam(":description", $description);
$stmt->bindParam(":event_date", $data->event_date);
$event_time = isset($data->event_time) ? $data->event_time : null;
$stmt->bindParam(":event_time", $event_time);
$end_date = isset($data->end_date) ? $data->end_date : null;
$stmt->bindParam(":end_date", $end_date);
$location = isset($data->location) ? $data->location : null;
$stmt->bindParam(":location", $location);
$category = isset($data->category) ? $data->category : 'Academic';
$stmt->bindParam(":category", $category);
$image_url = isset($data->image_url) ? $data->image_url : null;
$stmt->bindParam(":image_url", $image_url);
$status = isset($data->status) ? $data->status : 'Upcoming';
$stmt->bindParam(":status", $status);
$stmt->bindParam(":created_by", $user->id);

if ($stmt->execute()) {
    $event_id = $db->lastInsertId();
    
    $log_query = "INSERT INTO admin_activity_logs (user_id, action_type, table_name, record_id, description, ip_address) 
                  VALUES (:user_id, 'CREATE', 'events', :record_id, :description, :ip)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":user_id", $user->id);
    $log_stmt->bindParam(":record_id", $event_id);
    $description = "Created event: " . $data->title;
    $log_stmt->bindParam(":description", $description);
    $log_stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
    $log_stmt->execute();
    
    sendSuccess("Event created successfully", array("id" => $event_id), 201);
} else {
    sendError("Failed to create event", null, 500);
}
?>
