<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['student_name', 'date_of_birth', 'gender', 'parent_name', 'parent_email', 'parent_phone', 'class_applying'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
            ]);
            exit;
        }
    }
    
    // Validate email
    if (!filter_var($data['parent_email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Insert admission application
    $stmt = $db->prepare("
        INSERT INTO admission_applications 
        (student_name, date_of_birth, gender, parent_name, parent_email, parent_phone, 
         parent_address, class_applying, previous_school, medical_conditions, 
         application_status, application_date) 
        VALUES 
        (:student_name, :date_of_birth, :gender, :parent_name, :parent_email, :parent_phone,
         :parent_address, :class_applying, :previous_school, :medical_conditions,
         'pending', NOW())
    ");
    
    $stmt->bindParam(':student_name', $data['student_name']);
    $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':parent_name', $data['parent_name']);
    $stmt->bindParam(':parent_email', $data['parent_email']);
    $stmt->bindParam(':parent_phone', $data['parent_phone']);
    $address = $data['parent_address'] ?? '';
    $stmt->bindParam(':parent_address', $address);
    $stmt->bindParam(':class_applying', $data['class_applying']);
    $previous_school = $data['previous_school'] ?? '';
    $stmt->bindParam(':previous_school', $previous_school);
    $medical = $data['medical_conditions'] ?? '';
    $stmt->bindParam(':medical_conditions', $medical);
    
    if ($stmt->execute()) {
        $application_id = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully! We will contact you soon.',
            'application_id' => $application_id
        ]);
    } else {
        throw new Exception('Failed to save application');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
