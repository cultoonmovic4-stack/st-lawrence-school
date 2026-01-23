<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get active teachers
    $stmt = $db->prepare("
        SELECT 
            t.teacher_id,
            t.full_name,
            t.email,
            t.phone,
            t.subject_specialization,
            t.qualification,
            t.profile_photo,
            c.class_name
        FROM teachers t
        LEFT JOIN classes c ON t.class_id = c.class_id
        WHERE t.status = 'active'
        ORDER BY t.full_name ASC
    ");
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
