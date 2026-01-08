<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    sendError("No file uploaded or upload error", null, 400);
    exit();
}

$file = $_FILES['image'];

// Validate file type
$allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
$file_type = mime_content_type($file['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    sendError("Invalid file type. Only JPG and PNG allowed", null, 400);
    exit();
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024;
if ($file['size'] > $max_size) {
    sendError("File too large. Maximum size is 5MB", null, 400);
    exit();
}

// Get form data
$title = isset($_POST['title']) ? $_POST['title'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : 'Academics';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$size = isset($_POST['size']) ? $_POST['size'] : 'medium';

if (empty($title)) {
    sendError("Title is required", null, 400);
    exit();
}

// Create upload directory
$upload_dir = "../../uploads/gallery/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = "gallery_" . time() . "_" . bin2hex(random_bytes(8)) . "." . $extension;
$file_path = $upload_dir . $filename;
$relative_path = "uploads/gallery/" . $filename;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO gallery_images 
              (title, description, image_url, category, size, uploaded_by, status) 
              VALUES 
              (:title, :description, :image_url, :category, :size, :uploaded_by, 'active')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":image_url", $relative_path);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":size", $size);
    $stmt->bindParam(":uploaded_by", $user->id);
    
    if ($stmt->execute()) {
        $image_id = $db->lastInsertId();
        
        sendSuccess("Image uploaded successfully", array(
            "id" => $image_id,
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
