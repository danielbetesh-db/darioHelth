<?php
 

function sendEmail($to, $subject, $message, $from = "noreply@example.com") {
    $headers = "From: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}



function sendInvitationEmails($pdo) {
    try{
        $successCount = 0;
        $userManager = new UserManager($pdo);
        $users = $userManager->getNotApprovedUsers(); 
        foreach ($users as $user) {
            $link = getOrigin() . '/dario/public/?id=' . $user->uniqId;
            //@ to prevent error from returning to response in local env
            $emailResult = sendEmail($user->userEmail, 'Your devices are approved', HtmlTemplate::InviteEmailContent($user->userName, $link));
            if($emailResult) {
                $successCount++; 
            }
        }
        
        return [
            'successCount' => $successCount,
            'totalCount' => count($users),
        ];
    }catch(Exception $e){
        return [
            'successCount' => 0,
            'totalCount' => 0,
        ];
    }

}

function getOrigin() {
    $serverName = $_SERVER['SERVER_NAME'];
    if ($serverName == 'localhost' || $serverName == '127.0.0.1') {
        return 'http://' . $serverName;
    } else {
        return 'https://' . $serverName;
    }
}