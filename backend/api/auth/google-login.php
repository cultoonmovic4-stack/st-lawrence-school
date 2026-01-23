<?php
/**
 * Google OAuth Login Handler
 * Handles Google Sign-In authentication
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['credential'])) {
        throw new Exception('No credential provided');
    }
    
    $credential = $input['credential'];
    
    // Verify the Google JWT token
    $googleUser = verifyGoogleToken($credential);
    
    if (!$googleUser) {
        throw new Exception('Invalid Google token');
    }
    
    // Extract user information
    $email = $googleUser['email'];
    $name = $googleUser['name'];
    $googleId = $googleUser['sub'];
    $picture = $googleUser['picture'] ?? null;
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if user exists
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // User doesn't exist - create new user with Google account
        $query = "INSERT INTO users (email, name, google_id, profile_picture, role, created_at) 
                  VALUES (:email, :name, :google_id, :picture, 'viewer', NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':google_id', $googleId);
        $stmt->bindParam(':picture', $picture);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create user account');
        }
        
        // Get the newly created user
        $userId = $db->lastInsertId();
        $user = [
            'id' => $userId,
            'email' => $email,
            'name' => $name,
            'role' => 'viewer',
            'profile_picture' => $picture
        ];
    } else {
        // Update Google ID and picture if not set
        if (empty($user['google_id'])) {
            $query = "UPDATE users SET google_id = :google_id, profile_picture = :picture WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':google_id', $googleId);
            $stmt->bindParam(':picture', $picture);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
            
            $user['google_id'] = $googleId;
            $user['profile_picture'] = $picture;
        }
    }
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Store user data in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_method'] = 'google';
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Google login successful',
        'data' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'profile_picture' => $user['profile_picture']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Verify Google JWT Token
 * @param string $token The JWT token from Google
 * @return array|false User data or false on failure
 */
function verifyGoogleToken($token) {
    try {
        // Split the JWT token
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        // Decode the payload (second part)
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $userData = json_decode($payload, true);
        
        if (!$userData) {
            return false;
        }
        
        // Verify the token is not expired
        if (isset($userData['exp']) && $userData['exp'] < time()) {
            return false;
        }
        
        // Verify the issuer
        if (!isset($userData['iss']) || 
            ($userData['iss'] !== 'https://accounts.google.com' && 
             $userData['iss'] !== 'accounts.google.com')) {
            return false;
        }
        
        // Verify email is verified
        if (!isset($userData['email_verified']) || $userData['email_verified'] !== true) {
            return false;
        }
        
        return $userData;
        
    } catch (Exception $e) {
        return false;
    }
}
?>
