<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$resource_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($resource_id)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Resource ID is required"));
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM library_resources WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $resource_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Increment download count
    $update_query = "UPDATE library_resources SET download_count = download_count + 1 WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(":id", $resource_id);
    $update_stmt->execute();
    
    // Log download
    $log_query = "INSERT INTO download_logs (library_id, user_ip, user_agent) 
                  VALUES (:library_id, :user_ip, :user_agent)";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(":library_id", $resource_id);
    $log_stmt->bindParam(":user_ip", $_SERVER['REMOTE_ADDR']);
    $log_stmt->bindParam(":user_agent", $_SERVER['HTTP_USER_AGENT']);
    $log_stmt->execute();
    
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Download tracked successfully",
        "data" => array(
            "download_count" => $resource['download_count'] + 1
        )
    ));
} else {
    http_response_code(404);
    echo json_encode(array("success" => false, "message" => "Resource not found"));
}
?>
