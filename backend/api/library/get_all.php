<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("
        SELECT 
            lr.id,
            lr.title,
            lr.description,
            lr.category,
            lr.class_level,
            lr.subject,
            lr.file_url,
            lr.file_type,
            lr.file_size,
            lr.download_count,
            lr.upload_date,
            lr.status,
            lr.created_at,
            u.full_name as uploaded_by_name
        FROM library_resources lr
        LEFT JOIN users u ON lr.uploaded_by = u.id
        ORDER BY lr.created_at DESC
    ");
    
    $stmt->execute();
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $resources
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
