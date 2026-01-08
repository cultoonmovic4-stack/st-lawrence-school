<?php
include_once '../config/cors.php';
include_once '../config/database.php';

// Get query parameters
$department = isset($_GET['department']) ? $_GET['department'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

$database = new Database();
$db = $database->getConnection();

// Build query
$query = "SELECT * FROM teachers WHERE 1=1";

if (!empty($department)) {
    $query .= " AND department = :department";
}

if (!empty($search)) {
    $query .= " AND (name LIKE :search OR email LIKE :search OR position LIKE :search)";
}

if (!empty($status)) {
    $query .= " AND status = :status";
}

$query .= " ORDER BY display_order ASC, id DESC";

$stmt = $db->prepare($query);

// Bind parameters
if (!empty($department)) {
    $stmt->bindParam(":department", $department);
}

if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bindParam(":search", $search_param);
}

if (!empty($status)) {
    $stmt->bindParam(":status", $status);
}

$stmt->execute();

$teachers = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $teacher = array(
        "id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email'],
        "phone" => $row['phone'],
        "department" => $row['department'],
        "position" => $row['position'],
        "qualification" => $row['qualification'],
        "experience_years" => $row['experience_years'],
        "bio" => $row['bio'],
        "specialties" => $row['specialties'],
        "photo_url" => $row['photo_url'],
        "facebook" => $row['facebook'],
        "twitter" => $row['twitter'],
        "linkedin" => $row['linkedin'],
        "students_count" => $row['students_count'],
        "subjects_taught" => $row['subjects_taught'],
        "status" => $row['status'],
        "display_order" => $row['display_order'],
        "created_at" => $row['created_at']
    );
    
    array_push($teachers, $teacher);
}

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Teachers retrieved successfully",
    "count" => count($teachers),
    "data" => $teachers
));
?>
