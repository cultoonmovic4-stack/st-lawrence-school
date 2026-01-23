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
            id,
            application_id,
            student_first_name,
            student_last_name,
            date_of_birth,
            gender,
            nationality,
            religion,
            class_to_join,
            admission_type,
            parent_first_name,
            parent_last_name,
            parent_relationship,
            parent_phone,
            parent_email,
            parent_address,
            parent_occupation,
            emergency_contact_name,
            emergency_contact_relationship,
            emergency_contact_phone,
            birth_certificate_url,
            previous_school_report_url,
            passport_photo_url,
            immunization_record_url,
            parent_id_url,
            transfer_letter_url,
            status,
            submitted_date,
            reviewed_by,
            review_date,
            review_notes
        FROM admission_applications
        ORDER BY submitted_date DESC
    ");
    
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $applications
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
