<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM gallery_images WHERE status = 'active'";

if (!empty($category)) {
    $query .= " AND category = :category";
}

$query .= " ORDER BY display_order ASC, created_at DESC";

$stmt = $db->prepare($query);

if (!empty($category)) {
    $stmt->bindParam(":category", $category);
}

$stmt->execute();

$images = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($images, $row);
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Gallery images retrieved successfully",
    "count" => count($images),
    "data" => $images
));
?>
