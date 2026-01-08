<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->name) || empty($data->email) || empty($data->subject) || empty($data->message)) {
    sendError("All fields are required", null, 400);
    exit();
}

// Validate email
if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    sendError("Invalid email address", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Insert contact submission
$query = "INSERT INTO contact_submissions 
          (name, email, phone, subject, message, form_type, ip_address, status) 
          VALUES 
          (:name, :email, :phone, :subject, :message, :form_type, :ip_address, 'New')";

$stmt = $db->prepare($query);

$stmt->bindParam(":name", $data->name);
$stmt->bindParam(":email", $data->email);
$phone = isset($data->phone) ? $data->phone : null;
$stmt->bindParam(":phone", $phone);
$stmt->bindParam(":subject", $data->subject);
$stmt->bindParam(":message", $data->message);
$form_type = isset($data->form_type) ? $data->form_type : 'Contact';
$stmt->bindParam(":form_type", $form_type);
$stmt->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);

if ($stmt->execute()) {
    $contact_id = $db->lastInsertId();
    
    // TODO: Send email notification to admin
    // TODO: Send auto-reply email to user
    
    sendSuccess("Message sent successfully! We will get back to you soon.", array("id" => $contact_id), 201);
} else {
    sendError("Failed to send message. Please try again.", null, 500);
}
?>
