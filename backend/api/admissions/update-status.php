<?php
// Disable all error output to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any errors
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';
require_once '../middleware/auth_middleware.php';

// Clear any output that might have been generated
if (ob_get_length()) ob_clean();

// Check authentication
if (!isAuthenticated()) {
    ob_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    ob_end_flush();
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['id']) || empty($data['status'])) {
        throw new Exception('Application ID and status are required');
    }
    
    $validStatuses = ['pending', 'under_review', 'accepted', 'rejected', 'waitlist'];
    if (!in_array($data['status'], $validStatuses)) {
        throw new Exception('Invalid status');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get application details
    $stmt = $db->prepare("SELECT * FROM admission_applications WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        throw new Exception('Application not found');
    }
    
    // Update status
    $currentUser = getCurrentUser();
    $reviewNotes = $data['notes'] ?? '';
    $userId = $currentUser['user_id'] ?? null;
    
    $stmt = $db->prepare("
        UPDATE admission_applications 
        SET status = :status, 
            reviewed_by = :reviewed_by, 
            review_date = NOW(),
            review_notes = :review_notes
        WHERE id = :id
    ");
    
    $stmt->bindParam(':status', $data['status']);
    $stmt->bindParam(':reviewed_by', $userId);
    $stmt->bindParam(':review_notes', $reviewNotes);
    $stmt->bindParam(':id', $data['id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update status');
    }
    
    // Send email notification
    $emailStatus = 'No email to send';
    if (!empty($application['parent_email'])) {
        try {
            // Load PHPMailer and Email class
            require_once '../../vendor/autoload.php';
            require_once '../config/Email.php';
            
            // Log email attempt
            error_log("Attempting to send email to: " . $application['parent_email']);
            
            $email = new Email();
            $parentName = $application['parent_first_name'] . ' ' . $application['parent_last_name'];
            $studentName = $application['student_first_name'] . ' ' . $application['student_last_name'];
            
            $statusMessages = [
                'accepted' => [
                    'subject' => 'Admission Accepted - ' . $application['application_id'],
                    'message' => "
                        <p>Dear {$parentName},</p>
                        <p>We are delighted to inform you that <strong>{$studentName}</strong>'s application for admission to <strong>{$application['class_to_join']}</strong> has been <strong style='color: #10b981;'>ACCEPTED</strong>!</p>
                        <p><strong>Application ID:</strong> {$application['application_id']}</p>
                        <p>Welcome to the St. Lawrence family! We will contact you shortly with further details regarding enrollment procedures, fee payment, and the start date.</p>
                        " . ($reviewNotes ? "<p><strong>Additional Notes:</strong><br>{$reviewNotes}</p>" : "") . "
                        <p>If you have any questions, please don't hesitate to contact us.</p>
                    "
                ],
                'rejected' => [
                    'subject' => 'Admission Application Update - ' . $application['application_id'],
                    'message' => "
                        <p>Dear {$parentName},</p>
                        <p>Thank you for your interest in St. Lawrence Junior School - Kabowa.</p>
                        <p>After careful consideration, we regret to inform you that we are unable to offer admission to <strong>{$studentName}</strong> for <strong>{$application['class_to_join']}</strong> at this time.</p>
                        <p><strong>Application ID:</strong> {$application['application_id']}</p>
                        " . ($reviewNotes ? "<p><strong>Reason:</strong><br>{$reviewNotes}</p>" : "") . "
                        <p>We appreciate your understanding and wish {$studentName} all the best in their educational journey.</p>
                    "
                ],
                'waitlist' => [
                    'subject' => 'Application Waitlisted - ' . $application['application_id'],
                    'message' => "
                        <p>Dear {$parentName},</p>
                        <p>Thank you for applying to St. Lawrence Junior School - Kabowa.</p>
                        <p>We would like to inform you that <strong>{$studentName}</strong>'s application for <strong>{$application['class_to_join']}</strong> has been placed on our <strong style='color: #f59e0b;'>WAITLIST</strong>.</p>
                        <p><strong>Application ID:</strong> {$application['application_id']}</p>
                        <p>This means that while we were impressed with the application, we currently do not have available spaces. We will contact you immediately if a space becomes available.</p>
                        " . ($reviewNotes ? "<p><strong>Additional Information:</strong><br>{$reviewNotes}</p>" : "") . "
                        <p>Thank you for your patience and understanding.</p>
                    "
                ],
                'under_review' => [
                    'subject' => 'Application Under Review - ' . $application['application_id'],
                    'message' => "
                        <p>Dear {$parentName},</p>
                        <p>This is to inform you that <strong>{$studentName}</strong>'s application for <strong>{$application['class_to_join']}</strong> is currently <strong style='color: #0066cc;'>UNDER REVIEW</strong>.</p>
                        <p><strong>Application ID:</strong> {$application['application_id']}</p>
                        <p>Our admissions team is carefully reviewing the application. We will notify you of our decision within 5 working days.</p>
                        " . ($reviewNotes ? "<p><strong>Notes:</strong><br>{$reviewNotes}</p>" : "") . "
                        <p>Thank you for your patience.</p>
                    "
                ]
            ];
            
            if (isset($statusMessages[$data['status']])) {
                $emailData = $statusMessages[$data['status']];
                $emailSent = $email->sendEmail(
                    $application['parent_email'],
                    $emailData['subject'],
                    $emailData['message']
                );
                
                // Log result
                if ($emailSent) {
                    error_log("Email sent successfully to: " . $application['parent_email']);
                    $emailStatus = 'Email sent successfully';
                } else {
                    error_log("Email failed to send to: " . $application['parent_email']);
                    $emailStatus = 'Email failed to send';
                }
            }
        } catch (Exception $emailError) {
            // Log email error but don't fail the status update
            error_log('Email sending failed: ' . $emailError->getMessage());
            $emailStatus = 'Email error: ' . $emailError->getMessage();
        }
    } else {
        error_log("No parent email found for application ID: " . $data['id']);
        $emailStatus = 'No parent email found';
    }
    
    // Clear output buffer and send JSON
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully!',
        'email_status' => $emailStatus
    ]);
    ob_end_flush();
    
} catch (Exception $e) {
    // Clear any output buffer
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    ob_end_flush();
}
