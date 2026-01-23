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
    
    if (empty($data['submission_id']) || empty($data['reply_message'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    // Get original submission details
    $getStmt = $db->prepare("SELECT name, email, subject FROM contact_submissions WHERE id = :id");
    $getStmt->bindParam(':id', $data['submission_id']);
    $getStmt->execute();
    $submission = $getStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$submission) {
        throw new Exception('Submission not found');
    }
    
    // Insert reply
    $stmt = $db->prepare("
        INSERT INTO contact_replies 
        (contact_submission_id, replied_by, reply_message, reply_date) 
        VALUES 
        (:submission_id, :replied_by, :reply_message, NOW())
    ");
    
    $stmt->bindParam(':submission_id', $data['submission_id']);
    $stmt->bindParam(':replied_by', $userId);
    $stmt->bindParam(':reply_message', $data['reply_message']);
    
    if ($stmt->execute()) {
        // Update submission status to 'replied'
        $updateStmt = $db->prepare("
            UPDATE contact_submissions 
            SET status = 'replied', 
                replied_by = :replied_by, 
                reply_date = NOW(),
                reply_message = :reply_message
            WHERE id = :submission_id
        ");
        
        $updateStmt->bindParam(':replied_by', $userId);
        $updateStmt->bindParam(':reply_message', $data['reply_message']);
        $updateStmt->bindParam(':submission_id', $data['submission_id']);
        $updateStmt->execute();
        
        // Try to send email (if PHPMailer is installed)
        $emailSent = false;
        $emailError = null;
        if (file_exists('../../vendor/autoload.php')) {
            require_once '../../vendor/autoload.php';
            require_once '../config/Email.php';
            
            try {
                $emailer = new Email();
                $emailSent = $emailer->sendReply(
                    $submission['email'],
                    $submission['name'],
                    $submission['subject'],
                    $data['reply_message']
                );
            } catch (Exception $e) {
                $emailError = $e->getMessage();
                error_log('Email sending failed: ' . $emailError);
            }
        } else {
            $emailError = 'PHPMailer not found';
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Reply saved successfully' . ($emailSent ? ' and email sent' : ''),
            'email_sent' => $emailSent,
            'email_error' => $emailError
        ]);
    } else {
        throw new Exception('Failed to send reply');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
