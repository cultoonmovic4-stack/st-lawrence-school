<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? 'reading';
    $class_level = $_POST['class_level'] ?? 'all';
    $subject = $_POST['subject'] ?? '';
    $upload_date = $_POST['upload_date'] ?? date('Y-m-d');
    
    // Validate category if provided
    if (!empty($category)) {
        $validCategories = ['assignment', 'reading', 'past_exam', 'revision', 'multimedia', 'study_guide'];
        if (!in_array($category, $validCategories)) {
            throw new Exception('Invalid category');
        }
    }
    
    // Validate class level if provided
    if (!empty($class_level)) {
        $validLevels = ['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'all'];
        if (!in_array($class_level, $validLevels)) {
            throw new Exception('Invalid class level');
        }
    }
    
    // Handle file upload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }
    
    $file = $_FILES['file'];
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg',
        'image/png',
        'video/mp4'
    ];
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, MP4');
    }
    
    // Check file size (100MB max)
    $maxSize = 100 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 100MB limit');
    }
    
    // Create upload directory
    $uploadDir = __DIR__ . '/../../uploads/library/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'library_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Auto-generate title from filename if not provided
    if (empty($title)) {
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $title = ucwords(str_replace(['_', '-'], ' ', $originalName));
    }
    
    // Save to database
    $database = new Database();
    $db = $database->getConnection();
    
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    $fileUrl = 'uploads/library/' . $filename;
    $fileType = $extension;
    $fileSize = $file['size'];
    
    $stmt = $db->prepare("
        INSERT INTO library_resources 
        (title, description, category, class_level, subject, file_url, file_type, file_size, uploaded_by, upload_date, status) 
        VALUES 
        (:title, :description, :category, :class_level, :subject, :file_url, :file_type, :file_size, :uploaded_by, :upload_date, 'active')
    ");
    
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':class_level', $class_level);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':file_url', $fileUrl);
    $stmt->bindParam(':file_type', $fileType);
    $stmt->bindParam(':file_size', $fileSize);
    $stmt->bindParam(':uploaded_by', $userId);
    $stmt->bindParam(':upload_date', $upload_date);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Resource uploaded successfully!',
            'data' => [
                'id' => $db->lastInsertId(),
                'title' => $title,
                'file_url' => $fileUrl
            ]
        ]);
    } else {
        throw new Exception('Failed to save to database');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
