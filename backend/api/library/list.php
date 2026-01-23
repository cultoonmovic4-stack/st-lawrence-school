<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get filters if provided
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $class_level = isset($_GET['class_level']) ? $_GET['class_level'] : '';
    
    $sql = "
        SELECT 
            id,
            title,
            description,
            category,
            class_level,
            subject,
            file_url,
            file_type,
            file_size,
            download_count,
            upload_date
        FROM library_resources 
        WHERE status = 'active'
    ";
    
    if ($category && $category !== 'all') {
        $sql .= " AND category = :category";
    }
    
    if ($class_level && $class_level !== 'all') {
        $sql .= " AND (class_level = :class_level OR class_level = 'all')";
    }
    
    $sql .= " ORDER BY upload_date DESC, id DESC";
    
    $stmt = $db->prepare($sql);
    
    if ($category && $category !== 'all') {
        $stmt->bindParam(':category', $category);
    }
    
    if ($class_level && $class_level !== 'all') {
        $stmt->bindParam(':class_level', $class_level);
    }
    
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
