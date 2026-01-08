<?php
function sendSuccess($message, $data = null, $code = 200) {
    http_response_code($code);
    $response = array(
        "success" => true,
        "message" => $message
    );
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response);
}

function sendError($message, $errors = null, $code = 400) {
    http_response_code($code);
    $response = array(
        "success" => false,
        "message" => $message
    );
    if ($errors !== null) {
        $response["errors"] = $errors;
    }
    echo json_encode($response);
}
?>
