<?php
include_once '../config/cors.php';
include_once '../config/jwt.php';

// Get authorization header
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!empty($authHeader)) {
    $arr = explode(" ", $authHeader);
    $jwt = isset($arr[1]) ? $arr[1] : '';
    
    if ($jwt) {
        $decoded = JWT::decode($jwt);
        
        if ($decoded) {
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "Token is valid",
                "user" => $decoded
            ));
        } else {
            http_response_code(401);
            echo json_encode(array(
                "success" => false,
                "message" => "Invalid or expired token"
            ));
        }
    } else {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => "Token not provided"
        ));
    }
} else {
    http_response_code(401);
    echo json_encode(array(
        "success" => false,
        "message" => "Authorization header not found"
    ));
}
?>
