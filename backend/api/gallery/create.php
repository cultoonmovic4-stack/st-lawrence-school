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
    // Handle file upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Image upload failed');
    }
    
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed');
    }
    
    // Check file size (100MB max)
    $maxSize = 100 * 1024 * 1024; // 100MB in bytes
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 100MB limit');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../../uploads/gallery/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Auto-generate title from filename
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $title = ucwords(str_replace(['_', '-'], ' ', $originalName));
    
    // Default category
    $category = 'events';
    $description = '';
    $upload_date = date('Y-m-d');
    
    // Save to database
    $database = new Database();
    $db = $database->getConnection();
    
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    $stmt = $db->prepare("
        INSERT INTO gallery_images 
        (title, description, image_url, category, upload_date, uploaded_by, status) 
        VALUES 
        (:title, :description, :image_url, :category, :upload_date, :uploaded_by, 'active')
    ");
    
    $imageUrl = 'uploads/gallery/' . $filename;
    
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image_url', $imageUrl);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':upload_date', $upload_date);
    $stmt->bindParam(':uploaded_by', $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Gallery image uploaded successfully!',
            'data' => [
                'id' => $db->lastInsertId(),
                'image_url' => $imageUrl
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
