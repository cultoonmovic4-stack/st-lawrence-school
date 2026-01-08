<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
$required = ['application_id', 'student_first_name', 'student_last_name', 'date_of_birth', 
             'gender', 'class_to_join', 'parent_name', 'parent_phone', 'parent_email'];

foreach ($required as $field) {
    if (empty($data->$field)) {
        sendError("Field '$field' is required", null, 400);
        exit();
    }
}

// Validate email
if (!filter_var($data->parent_email, FILTER_VALIDATE_EMAIL)) {
    sendError("Invalid parent email address", null, 400);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if application ID already exists
$check_query = "SELECT id FROM admission_applications WHERE application_id = :application_id";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(":application_id", $data->application_id);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    sendError("Application ID already exists", null, 400);
    exit();
}

// Insert admission application
$query = "INSERT INTO admission_applications 
          (application_id, student_first_name, student_last_name, date_of_birth, gender, 
           nationality, religion, class_to_join, medical_conditions,
           parent_name, parent_relationship, parent_phone, parent_email, parent_occupation, 
           parent_nin, parent_address,
           emergency_name, emergency_relationship, emergency_phone, emergency_email,
           previous_school, last_class, reason_for_leaving,
           how_heard, comments, status) 
          VALUES 
          (:application_id, :student_first_name, :student_last_name, :date_of_birth, :gender,
           :nationality, :religion, :class_to_join, :medical_conditions,
           :parent_name, :parent_relationship, :parent_phone, :parent_email, :parent_occupation,
           :parent_nin, :parent_address,
           :emergency_name, :emergency_relationship, :emergency_phone, :emergency_email,
           :previous_school, :last_class, :reason_for_leaving,
           :how_heard, :comments, 'Pending')";

$stmt = $db->prepare($query);

// Bind all parameters
$stmt->bindParam(":application_id", $data->application_id);
$stmt->bindParam(":student_first_name", $data->student_first_name);
$stmt->bindParam(":student_last_name", $data->student_last_name);
$stmt->bindParam(":date_of_birth", $data->date_of_birth);
$stmt->bindParam(":gender", $data->gender);
$nationality = isset($data->nationality) ? $data->nationality : null;
$stmt->bindParam(":nationality", $nationality);
$religion = isset($data->religion) ? $data->religion : null;
$stmt->bindParam(":religion", $religion);
$stmt->bindParam(":class_to_join", $data->class_to_join);
$medical_conditions = isset($data->medical_conditions) ? $data->medical_conditions : null;
$stmt->bindParam(":medical_conditions", $medical_conditions);

$stmt->bindParam(":parent_name", $data->parent_name);
$parent_relationship = isset($data->parent_relationship) ? $data->parent_relationship : null;
$stmt->bindParam(":parent_relationship", $parent_relationship);
$stmt->bindParam(":parent_phone", $data->parent_phone);
$stmt->bindParam(":parent_email", $data->parent_email);
$parent_occupation = isset($data->parent_occupation) ? $data->parent_occupation : null;
$stmt->bindParam(":parent_occupation", $parent_occupation);
$parent_nin = isset($data->parent_nin) ? $data->parent_nin : null;
$stmt->bindParam(":parent_nin", $parent_nin);
$parent_address = isset($data->parent_address) ? $data->parent_address : null;
$stmt->bindParam(":parent_address", $parent_address);

$emergency_name = isset($data->emergency_name) ? $data->emergency_name : null;
$stmt->bindParam(":emergency_name", $emergency_name);
$emergency_relationship = isset($data->emergency_relationship) ? $data->emergency_relationship : null;
$stmt->bindParam(":emergency_relationship", $emergency_relationship);
$emergency_phone = isset($data->emergency_phone) ? $data->emergency_phone : null;
$stmt->bindParam(":emergency_phone", $emergency_phone);
$emergency_email = isset($data->emergency_email) ? $data->emergency_email : null;
$stmt->bindParam(":emergency_email", $emergency_email);

$previous_school = isset($data->previous_school) ? $data->previous_school : null;
$stmt->bindParam(":previous_school", $previous_school);
$last_class = isset($data->last_class) ? $data->last_class : null;
$stmt->bindParam(":last_class", $last_class);
$reason_for_leaving = isset($data->reason_for_leaving) ? $data->reason_for_leaving : null;
$stmt->bindParam(":reason_for_leaving", $reason_for_leaving);

$how_heard = isset($data->how_heard) ? $data->how_heard : null;
$stmt->bindParam(":how_heard", $how_heard);
$comments = isset($data->comments) ? $data->comments : null;
$stmt->bindParam(":comments", $comments);

if ($stmt->execute()) {
    $application_id = $db->lastInsertId();
    
    // TODO: Send confirmation email to parent
    // TODO: Send notification email to admissions office
    
    sendSuccess("Application submitted successfully!", array("id" => $application_id), 201);
} else {
    sendError("Failed to submit application. Please try again.", null, 500);
}
?>
