<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

try {
    if (empty($_GET['id'])) {
        throw new Exception('Resource ID is required');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get resource info
    $stmt = $db->prepare("SELECT file_url, title, file_type FROM library_resources WHERE id = :id AND status = 'active'");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resource) {
        throw new Exception('Resource not found');
    }
    
    $filePath = __DIR__ . '/../../' . $resource['file_url'];
    
    if (!file_exists($filePath)) {
        throw new Exception('File not found');
    }
    
    // Increment download count
    $updateStmt = $db->prepare("UPDATE library_resources SET download_count = download_count + 1 WHERE id = :id");
    $updateStmt->bindParam(':id', $_GET['id']);
    $updateStmt->execute();
    
    // Set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($resource['title']) . '.' . $resource['file_type'] . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Output file
    readfile($filePath);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
}
