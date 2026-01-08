<?php
include_once '../config/cors.php';
include_once '../config/database.php';

// Get query parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$upcoming = isset($_GET['upcoming']) ? $_GET['upcoming'] : '';

$database = new Database();
$db = $database->getConnection();

// Build query
$query = "SELECT * FROM events WHERE 1=1";

if (!empty($status)) {
    $query .= " AND status = :status";
}

if (!empty($category)) {
    $query .= " AND category = :category";
}

if ($upcoming === 'true') {
    $query .= " AND event_date >= CURDATE()";
}

$query .= " ORDER BY event_date ASC";

$stmt = $db->prepare($query);

if (!empty($status)) {
    $stmt->bindParam(":status", $status);
}

if (!empty($category)) {
    $stmt->bindParam(":category", $category);
}

$stmt->execute();

$events = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($events, $row);
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Events retrieved successfully",
    "count" => count($events),
    "data" => $events
));
?>
