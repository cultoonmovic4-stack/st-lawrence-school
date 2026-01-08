<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

// Get teacher ID from URL
$teacher_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($teacher_id)) {
    sendError("Teacher ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM teachers WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $teacher_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $teacher = array(
        "id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email'],
        "phone" => $row['phone'],
        "department" => $row['department'],
        "position" => $row['position'],
        "qualification" => $row['qualification'],
        "experience_years" => $row['experience_years'],
        "bio" => $row['bio'],
        "specialties" => $row['specialties'],
        "photo_url" => $row['photo_url'],
        "facebook" => $row['facebook'],
        "twitter" => $row['twitter'],
        "linkedin" => $row['linkedin'],
        "students_count" => $row['students_count'],
        "subjects_taught" => $row['subjects_taught'],
        "status" => $row['status'],
        "display_order" => $row['display_order'],
        "created_at" => $row['created_at'],
        "updated_at" => $row['updated_at']
    );
    
    sendSuccess("Teacher retrieved successfully", $teacher);
} else {
    sendError("Teacher not found", null, 404);
}
?>
