<?php
include '../../src/config.php';


$method = $_SERVER["REQUEST_METHOD"];
 
// Handle GET and POST requests
switch ($method) {
 
    case 'POST':
        $inputJSON = file_get_contents('php://input');
        $reqData = json_decode($inputJSON, true);  
        if(!isset($reqData['action'])) {
            if(!isset($_POST['action'])){
                errorResponse('Invalid request data');
            }else{
                handlePostRequest($_POST, $pdo);
            }
        }else{
            handlePostRequest($reqData, $pdo);
        }
        break;
    default:
         errorResponse("Invalid request method");
         break;
}
 
 
function handlePostRequest($args, $pdo) {
    $action = $args['action'];
    switch($action){
        case "sendEmailsToUsers":
            $result = sendInvitationEmails($pdo);
            successResponse($result);
            break;
        case "importUsers":
            $file = isset($_FILES['file']) ? $_FILES['file'] : null;
            if ($file) { 
                if ($file['type'] != 'application/json') {
                    errorResponse('Invalid file type. Please upload a JSON file.');
                }
                $destinationPath = '../../upload/' . $file['name']; 
                if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
                    ImportJsonDataToDB($pdo, $destinationPath);
                } else {
                    errorResponse("Error moving the uploaded file.");
                }
            } else {
                errorResponse("No file uploaded.");
            }
            break;
        default:
            errorResponse("Invalid request data");
            break;
    }
 
}

function successResponse($data) {
    echo json_encode(["data" => $data]);
}

function errorResponse($message) {
    echo json_encode(["error" => $message]);
}

?>
