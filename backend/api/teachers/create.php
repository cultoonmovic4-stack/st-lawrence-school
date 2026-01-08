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
if (empty($data->name) || empty($data->email) || empty($data->department)) {
    sendError("Name, email, and department are required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if email already exists
$check_query = "SELECT id FROM teachers WHERE email = :email";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":email", $data->email);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    sendError("Email already exists", null, 400);
    exit();
}

// Insert teacher
$query = "INSERT INTO teachers 
          (name, email, phone, department, position, qualification, experience_years, 
           bio, specialties, photo_url, facebook, twitter, linkedin, 
           students_count, subjects_taught, status, display_order) 
          VALUES 
          (:name, :email, :phone, :department, :position, :qualification, :experience_years,
           :bio, :specialties, :photo_url, :facebook, :twitter, :linkedin,
           :students_count, :subjects_taught, :status, :display_order)";

$stmt = $db->prepare($query);

// Bind values
$stmt->bindParam(":name", $data->name);
$stmt->bindParam(":email", $data->email);
$phone = isset($data->phone) ? $data->phone : null;
$stmt->bindParam(":phone", $phone);
$stmt->bindParam(":department", $data->department);
$position = isset($data->position) ? $data->position : null;
$stmt->bindParam(":position", $position);
$qualification = isset($data->qualification) ? $data->qualification : null;
$stmt->bindParam(":qualification", $qualification);
$experience_years = isset($data->experience_years) ? $data->experience_years : 0;
$stmt->bindParam(":experience_years", $experience_years);
$bio = isset($data->bio) ? $data->bio : null;
$stmt->bindParam(":bio", $bio);
$specialties = isset($data->specialties) ? $data->specialties : null;
$stmt->bindParam(":specialties", $specialties);
$photo_url = isset($data->photo_url) ? $data->photo_url : null;
$stmt->bindParam(":photo_url", $photo_url);
$facebook = isset($data->facebook) ? $data->facebook : null;
$stmt->bindParam(":facebook", $facebook);
$twitter = isset($data->twitter) ? $data->twitter : null;
$stmt->bindParam(":twitter", $twitter);
$linkedin = isset($data->linkedin) ? $data->linkedin : null;
$stmt->bindParam(":linkedin", $linkedin);
$students_count = isset($data->students_count) ? $data->students_count : 0;
$stmt->bindParam(":students_count", $students_count);
$subjects_taught = isset($data->subjects_taught) ? $data->subjects_taught : 0;
$stmt->bindParam(":subjects_taught", $subjects_taught);
$status = isset($data->status) ? $data->status : 'active';
$stmt->bindParam(":status", $status);
$display_order = isset($data->display_order) ? $data->display_order : 0;
$stmt->bindParam(":display_order", $display_order);

if ($stmt->execute()) {
    $teacher_id = $db->lastInsertId();
    
    // Log activity
    $log_query = "INSERT INTO admin_activity_logs (user_id, action_type, table_name, record_id, description, ip_address) 
                  VALUES (:user_id, 'CREATE', 'teachers', :record_id, :description, :ip)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":user_id", $user->id);
    $log_stmt->bindParam(":record_id", $teacher_id);
    $description = "Created teacher: " . $data->name;
    $log_stmt->bindParam(":description", $description);
    $log_stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
    $log_stmt->execute();
    
    sendSuccess("Teacher created successfully", array("id" => $teacher_id), 201);
} else {
    sendError("Failed to create teacher", null, 500);
}
?>
