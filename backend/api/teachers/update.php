<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

// Verify token and require admin
$user = verifyToken();
requireAdmin($user);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->id)) {
    sendError("Teacher ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if teacher exists
$check_query = "SELECT id FROM teachers WHERE id = :id";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":id", $data->id);
$check_stmt->execute();

if ($check_stmt->rowCount() == 0) {
    sendError("Teacher not found", null, 404);
    exit();
}

// Build update query dynamically
$updates = array();
$params = array(":id" => $data->id);

if (isset($data->name)) {
    $updates[] = "name = :name";
    $params[":name"] = $data->name;
}
if (isset($data->email)) {
    $updates[] = "email = :email";
    $params[":email"] = $data->email;
}
if (isset($data->phone)) {
    $updates[] = "phone = :phone";
    $params[":phone"] = $data->phone;
}
if (isset($data->department)) {
    $updates[] = "department = :department";
    $params[":department"] = $data->department;
}
if (isset($data->position)) {
    $updates[] = "position = :position";
    $params[":position"] = $data->position;
}
if (isset($data->qualification)) {
    $updates[] = "qualification = :qualification";
    $params[":qualification"] = $data->qualification;
}
if (isset($data->experience_years)) {
    $updates[] = "experience_years = :experience_years";
    $params[":experience_years"] = $data->experience_years;
}
if (isset($data->bio)) {
    $updates[] = "bio = :bio";
    $params[":bio"] = $data->bio;
}
if (isset($data->specialties)) {
    $updates[] = "specialties = :specialties";
    $params[":specialties"] = $data->specialties;
}
if (isset($data->photo_url)) {
    $updates[] = "photo_url = :photo_url";
    $params[":photo_url"] = $data->photo_url;
}
if (isset($data->facebook)) {
    $updates[] = "facebook = :facebook";
    $params[":facebook"] = $data->facebook;
}
if (isset($data->twitter)) {
    $updates[] = "twitter = :twitter";
    $params[":twitter"] = $data->twitter;
}
if (isset($data->linkedin)) {
    $updates[] = "linkedin = :linkedin";
    $params[":linkedin"] = $data->linkedin;
}
if (isset($data->students_count)) {
    $updates[] = "students_count = :students_count";
    $params[":students_count"] = $data->students_count;
}
if (isset($data->subjects_taught)) {
    $updates[] = "subjects_taught = :subjects_taught";
    $params[":subjects_taught"] = $data->subjects_taught;
}
if (isset($data->status)) {
    $updates[] = "status = :status";
    $params[":status"] = $data->status;
}
if (isset($data->display_order)) {
    $updates[] = "display_order = :display_order";
    $params[":display_order"] = $data->display_order;
}

if (empty($updates)) {
    sendError("No fields to update", null, 400);
    exit();
}

$query = "UPDATE teachers SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = :id";
$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

if ($stmt->execute()) {
    // Log activity
    $log_query = "INSERT INTO admin_activity_logs (user_id, action_type, table_name, record_id, description, ip_address) 
                  VALUES (:user_id, 'UPDATE', 'teachers', :record_id, :description, :ip)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":user_id", $user->id);
    $log_stmt->bindParam(":record_id", $data->id);
    $description = "Updated teacher ID: " . $data->id;
    $log_stmt->bindParam(":description", $description);
    $log_stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
    $log_stmt->execute();
    
    sendSuccess("Teacher updated successfully");
} else {
    sendError("Failed to update teacher", null, 500);
}
?>
