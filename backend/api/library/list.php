<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$class_level = isset($_GET['class_level']) ? $_GET['class_level'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM library_resources WHERE status = 'active'";

if (!empty($class_level)) {
    $query .= " AND class_level = :class_level";
}

if (!empty($subject)) {
    $query .= " AND subject = :subject";
}

if (!empty($search)) {
    $query .= " AND (title LIKE :search OR description LIKE :search)";
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);

if (!empty($class_level)) {
    $stmt->bindParam(":class_level", $class_level);
}

if (!empty($subject)) {
    $stmt->bindParam(":subject", $subject);
}

if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bindParam(":search", $search_param);
}

$stmt->execute();

$resources = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($resources, $row);
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Library resources retrieved successfully",
    "count" => count($resources),
    "data" => $resources
));
?>
