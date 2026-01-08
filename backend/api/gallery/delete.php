<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_middleware.php';
include_once '../utils/response.php';

$user = verifyToken();
requireAdmin($user);

$image_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($image_id)) {
    sendError("Image ID is required", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$check_query = "SELECT image_url FROM gallery_images WHERE id = :id";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":id", $image_id);
$check_stmt->execute();

if ($check_stmt->rowCount() == 0) {
    sendError("Image not found", null, 404);
    exit();
}

$image = $check_stmt->fetch(PDO::FETCH_ASSOC);

$query = "DELETE FROM gallery_images WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $image_id);

if ($stmt->execute()) {
    if (!empty($image['image_url']) && file_exists("../../" . $image['image_url'])) {
        unlink("../../" . $image['image_url']);
    }
    
    sendSuccess("Image deleted successfully");
} else {
    sendError("Failed to delete image", null, 500);
}
?>
