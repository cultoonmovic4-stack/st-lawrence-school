<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get department filter if provided
    $department = isset($_GET['department']) ? $_GET['department'] : '';
    
    $sql = "
        SELECT 
            id,
            full_name as name,
            department,
            position,
            qualification,
            experience_years,
            email,
            phone,
            photo_url,
            bio,
            specialization as specialties,
            display_order
        FROM teachers 
        WHERE status = 'active'
    ";
    
    if ($department && $department !== 'all') {
        $sql .= " AND department = :department";
    }
    
    $sql .= " ORDER BY display_order DESC, full_name ASC";
    
    $stmt = $db->prepare($sql);
    
    if ($department && $department !== 'all') {
        $stmt->bindParam(':department', $department);
    }
    
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
