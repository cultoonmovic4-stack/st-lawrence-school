<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get only approved testimonials
$query = "SELECT * FROM testimonials WHERE status = 'Approved' ORDER BY approved_date DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();

$testimonials = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($testimonials, $row);
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Testimonials retrieved successfully",
    "count" => count($testimonials),
    "data" => $testimonials
));
?>
