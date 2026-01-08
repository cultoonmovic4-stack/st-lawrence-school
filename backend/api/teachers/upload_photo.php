<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

// Verify token and require admin
$user = verifyToken();
requireAdmin($user);

// Check if file was uploaded
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    sendError("No file uploaded or upload error", null, 400);
    exit();
}

$file = $_FILES['photo'];
$teacher_id = isset($_POST['teacher_id']) ? $_POST['teacher_id'] : null;

// Validate file type
$allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
$file_type = mime_content_type($file['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    sendError("Invalid file type. Only JPG and PNG allowed", null, 400);
    exit();
}

// Validate file size (max 2MB)
$max_size = 2 * 1024 * 1024; // 2MB in bytes
if ($file['size'] > $max_size) {
    sendError("File too large. Maximum size is 2MB", null, 400);
    exit();
}

// Create upload directory if not exists
$upload_dir = "../../uploads/teachers/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = "teacher_" . time() . "_" . bin2hex(random_bytes(8)) . "." . $extension;
$file_path = $upload_dir . $filename;
$relative_path = "uploads/teachers/" . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    
    // If teacher_id provided, update teacher record
    if ($teacher_id) {
        $database = new Database();
        $db = $database->getConnection();
        
        // Get old photo to delete
        $check_query = "SELECT photo_url FROM teachers WHERE id = :id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":id", $teacher_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $old_teacher = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete old photo
            if (!empty($old_teacher['photo_url']) && file_exists("../../" . $old_teacher['photo_url'])) {
                unlink("../../" . $old_teacher['photo_url']);
            }
            
            // Update teacher photo
            $update_query = "UPDATE teachers SET photo_url = :photo_url WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(":photo_url", $relative_path);
            $update_stmt->bindParam(":id", $teacher_id);
            $update_stmt->execute();
        }
    }
    
    // Log file upload
    $database = new Database();
    $db = $database->getConnection();
    
    $log_query = "INSERT INTO file_uploads 
                  (file_name, original_name, file_path, file_type, file_size, mime_type, uploaded_by, related_table, related_id) 
                  VALUES 
                  (:file_name, :original_name, :file_path, :file_type, :file_size, :mime_type, :uploaded_by, :related_table, :related_id)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":file_name", $filename);
    $log_stmt->bindParam(":original_name", $file['name']);
    $log_stmt->bindParam(":file_path", $relative_path);
    $file_type_str = "image";
    $log_stmt->bindParam(":file_type", $file_type_str);
    $log_stmt->bindParam(":file_size", $file['size']);
    $log_stmt->bindParam(":mime_type", $file_type);
    $log_stmt->bindParam(":uploaded_by", $user->id);
    $related_table = "teachers";
    $log_stmt->bindParam(":related_table", $related_table);
    $log_stmt->bindParam(":related_id", $teacher_id);
    $log_stmt->execute();
    
    sendSuccess("Photo uploaded successfully", array(
        "filename" => $filename,
        "path" => $relative_path,
        "url" => "http://" . $_SERVER['HTTP_HOST'] . "/" . str_replace("\\", "/", $relative_path)
    ), 201);
} else {
    sendError("Failed to upload file", null, 500);
}
?>
