<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../config/jwt.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if user exists
    $query = "SELECT id, username, email, password, role, status FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if (password_verify($data->password, $row['password'])) {
            
            // Check if user is active
            if ($row['status'] == 'active') {
                
                // Generate JWT token
                $token_data = array(
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "email" => $row['email'],
                    "role" => $row['role']
                );
                
                $jwt = JWT::encode($token_data);
                
                // Update last login
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = :id";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(":id", $row['id']);
                $update_stmt->execute();
                
                // Log activity
                $log_query = "INSERT INTO admin_activity_logs (user_id, action_type, description, ip_address) 
                              VALUES (:user_id, 'LOGIN', 'User logged in', :ip)";
                $log_stmt = $db->prepare($log_query);
                $log_stmt->bindParam(":user_id", $row['id']);
                $log_stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
                $log_stmt->execute();
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Login successful",
                    "token" => $jwt,
                    "user" => array(
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email'],
                        "role" => $row['role']
                    )
                ));
                
            } else {
                http_response_code(401);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Account is inactive"
                ));
            }
            
        } else {
            http_response_code(401);
            echo json_encode(array(
                "success" => false,
                "message" => "Invalid password"
            ));
        }
        
    } else {
        http_response_code(404);
        echo json_encode(array(
            "success" => false,
            "message" => "User not found"
        ));
    }
    
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Email and password are required"
    ));
}
?>
