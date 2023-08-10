<?php
include '../../src/config.php';

$method = $_SERVER["REQUEST_METHOD"];

// Handle GET and POST requests
switch ($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            handleGetRequest($_GET, $pdo);
        }else{
            errorResponse("No user id provided");
        }
        break;
    case 'POST':
        $inputJSON = file_get_contents('php://input');
        $reqData = json_decode($inputJSON, true); 
        if(!isset($reqData['userId']) || !isset($reqData['userEmail']) || !isset($reqData['uniqId'])) {
            errorResponse('Invalid request data');
        }else{
            handlePostRequest($reqData, $pdo);
        }
        break;
    default:
         errorResponse("Invalid request method");
         break;
}
// Get user by ID as JSON
function handleGetRequest($args, $pdo) {
    $userManager = new UserManager($pdo);
    $user = $userManager->getUserWithDevices($args['id']);
    successResponse($user); 
}

// Validate user and send email
// return user and html template if success
function handlePostRequest($args, $pdo) {
    $userManager = new UserManager($pdo);
    $user = $userManager->getUserWithDevices($args['uniqId']);
    if($user->userId === $args['userId'] && $user->userEmail === $args['userEmail']) {
       // used approved to avoid sending email multiple times
        if($user->approved === 0){
            $subject = "Your devices are approved";
            try{
                //@ to prevent error from returning to response in local env
                $emailResult = sendEmail($user->userEmail, $subject, HtmlTemplate::SuccessEmailContent($user->userName, $user->devices));
                if($emailResult) {
                    $userManager->setApproved($args['uniqId'], 1);
                }     
            }catch(Exception $e) {
                errorResponse("error sending email");
            }
                  
        }
        //Set user to session
        $_SESSION['user'] = $user;
        successResponse(['user' => $user, 'html' => HtmlTemplate::UserDeviceView($user)]); 
    }else{
        errorResponse("User not found, please check your details or contact your employer/payer/plan");
    } 
}

function successResponse($data) {
    echo json_encode(["data" => $data]);
}

function errorResponse($message) {
    echo json_encode(["error" => $message]);
}

?>
