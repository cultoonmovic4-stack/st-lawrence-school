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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['id'])) {
        throw new Exception('Application ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get application to delete files
    $stmt = $db->prepare("SELECT * FROM admission_applications WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        throw new Exception('Application not found');
    }
    
    // Delete files
    $fileFields = ['birth_certificate_url', 'previous_school_report_url', 'passport_photo_url', 
                   'immunization_record_url', 'parent_id_url', 'transfer_letter_url'];
    
    foreach ($fileFields as $field) {
        if (!empty($application[$field])) {
            $filePath = __DIR__ . '/../../' . $application[$field];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM admission_applications WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Application deleted successfully!'
        ]);
    } else {
        throw new Exception('Failed to delete application');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
