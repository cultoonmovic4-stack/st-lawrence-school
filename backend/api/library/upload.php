<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    sendError("No file uploaded or upload error", null, 400);
    exit();
}

$file = $_FILES['pdf'];

// Validate file type
if ($file['type'] !== 'application/pdf') {
    sendError("Invalid file type. Only PDF allowed", null, 400);
    exit();
}

// Validate file size (max 10MB)
$max_size = 10 * 1024 * 1024;
if ($file['size'] > $max_size) {
    sendError("File too large. Maximum size is 10MB", null, 400);
    exit();
}

// Get form data
$title = isset($_POST['title']) ? $_POST['title'] : '';
$class_level = isset($_POST['class_level']) ? $_POST['class_level'] : '';
$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

if (empty($title) || empty($class_level) || empty($subject)) {
    sendError("Title, class level, and subject are required", null, 400);
    exit();
}

// Create upload directory
$upload_dir = "../../uploads/library/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$filename = "pdf_" . time() . "_" . bin2hex(random_bytes(8)) . ".pdf";
$file_path = $upload_dir . $filename;
$relative_path = "uploads/library/" . $filename;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO library_resources 
              (title, file_name, file_path, file_size, class_level, subject, description, uploaded_by, status) 
              VALUES 
              (:title, :file_name, :file_path, :file_size, :class_level, :subject, :description, :uploaded_by, 'active')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":file_name", $filename);
    $stmt->bindParam(":file_path", $relative_path);
    $stmt->bindParam(":file_size", $file['size']);
    $stmt->bindParam(":class_level", $class_level);
    $stmt->bindParam(":subject", $subject);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":uploaded_by", $user->id);
    
    if ($stmt->execute()) {
        $resource_id = $db->lastInsertId();
        
        sendSuccess("PDF uploaded successfully", array(
            "id" => $resource_id,
            "filename" => $filename,
            "path" => $relative_path
        ), 201);
    } else {
        unlink($file_path);
        sendError("Failed to save to database", null, 500);
    }
} else {
    sendError("Failed to upload file", null, 500);
}
?>
