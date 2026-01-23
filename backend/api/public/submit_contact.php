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
    if (empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['message'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Insert contact submission
    $stmt = $db->prepare("
        INSERT INTO contact_submissions 
        (name, email, phone, subject, message, status, submitted_date) 
        VALUES 
        (:name, :email, :phone, :subject, :message, 'new', NOW())
    ");
    
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $phone = $data['phone'] ?? '';
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':subject', $data['subject']);
    $stmt->bindParam(':message', $data['message']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.'
        ]);
    } else {
        throw new Exception('Failed to save message');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
