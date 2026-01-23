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
    $currentUser = getCurrentUser();
    $userId = $currentUser['user_id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Handle file upload or form data
    if (isset($_FILES['profile_image'])) {
        // File upload with FormData
        $fullName = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Handle profile image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/profiles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
            }
            
            $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                $profileImage = 'backend/uploads/profiles/' . $fileName;
                
                // Delete old profile image if exists
                $stmt = $db->prepare("SELECT profile_image FROM users WHERE id = :id");
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $oldUser = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($oldUser && $oldUser['profile_image'] && file_exists('../../' . $oldUser['profile_image'])) {
                    unlink('../../' . $oldUser['profile_image']);
                }
            }
        }
        
        // Update user profile
        if ($profileImage) {
            $stmt = $db->prepare("
                UPDATE users 
                SET full_name = :full_name, 
                    email = :email, 
                    phone = :phone,
                    profile_image = :profile_image
                WHERE id = :id
            ");
            $stmt->bindParam(':profile_image', $profileImage);
        } else {
            $stmt = $db->prepare("
                UPDATE users 
                SET full_name = :full_name, 
                    email = :email, 
                    phone = :phone
                WHERE id = :id
            ");
        }
        
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $userId);
        
    } else {
        // JSON data without file
        $data = json_decode(file_get_contents('php://input'), true);
        
        $fullName = $data['full_name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        
        $stmt = $db->prepare("
            UPDATE users 
            SET full_name = :full_name, 
                email = :email, 
                phone = :phone
            WHERE id = :id
        ");
        
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $userId);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    } else {
        throw new Exception('Failed to update profile');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
