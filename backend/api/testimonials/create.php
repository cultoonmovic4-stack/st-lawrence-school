<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    // Get form data
    $parent_name = $_POST['parent_name'] ?? '';
    $testimonial_type = $_POST['testimonial_type'] ?? '';
    $testimonial_text = $_POST['testimonial_text'] ?? '';
    $rating = $_POST['rating'] ?? '';
    
    // Validate required fields
    if (empty($parent_name) || empty($testimonial_type) || empty($testimonial_text) || empty($rating)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'All required fields must be filled'
        ]);
        exit;
    }
    
    // Handle file upload
    $photo_url = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../img/testimonials/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }
        
        if ($_FILES['photo']['size'] > 100 * 1024 * 1024) { // 100MB limit
            throw new Exception('File size must be less than 100MB.');
        }
        
        $new_filename = 'testimonial_' . time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            $photo_url = 'img/testimonials/' . $new_filename;
        }
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Insert testimonial
    $stmt = $db->prepare("
        INSERT INTO testimonials 
        (parent_name, parent_role, testimonial_text, rating, photo_url, status, created_at) 
        VALUES 
        (:parent_name, :parent_role, :testimonial_text, :rating, :photo_url, 'approved', NOW())
    ");
    
    $stmt->bindParam(':parent_name', $parent_name);
    $stmt->bindParam(':parent_role', $testimonial_type);
    $stmt->bindParam(':testimonial_text', $testimonial_text);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':photo_url', $photo_url);
    
    if ($stmt->execute()) {
        $testimonial_id = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Testimonial added successfully',
            'testimonial_id' => $testimonial_id,
            'photo_url' => $photo_url
        ]);
    } else {
        throw new Exception('Failed to add testimonial');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
