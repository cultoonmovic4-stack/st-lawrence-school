<?php
include_once '../config/jwt.php';

function verifyToken() {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (empty($authHeader)) {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => "Authorization header not found"
        ));
        exit();
    }
    
    $arr = explode(" ", $authHeader);
    $jwt = isset($arr[1]) ? $arr[1] : '';
    
    if (empty($jwt)) {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => "Token not provided"
        ));
        exit();
    }
    
    $decoded = JWT::decode($jwt);
    
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid or expired token"
        ));
        exit();
    }
    
    return $decoded;
}

function requireAdmin($user) {
    if ($user->role !== 'Admin') {
        http_response_code(403);
        echo json_encode(array(
            "success" => false,
            "message" => "Admin access required"
        ));
        exit();
    }
}
?>
