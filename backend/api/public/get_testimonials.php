<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get approved testimonials
    $stmt = $db->prepare("
        SELECT 
            id,
            parent_name,
            parent_role,
            testimonial_text,
            rating,
            photo_url
        FROM testimonials
        WHERE status = 'approved'
        ORDER BY display_order ASC, created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $testimonials
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
