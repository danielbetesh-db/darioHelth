<?php

class HtmlTemplate {

    public static function UserDeviceView($user) {
        $html = '<div class="user-view">';
        $html .= '<h2>Hi ' . $user->userName . '</h2>';
        $html .= '<p><b>Email:</b>' . $user->userEmail . '</p>';
        $html .= '<p>Here is you devices: </p>';
        $html .= '<ul>';
        foreach ($user->devices as $device) {
            $html .= '<li>' . $device . '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    
        
    }

    public static function SuccessEmailContent($userName, $devices){
        $deviceList = implode(', ', $devices);
        $content = "          
            <p>Dear {$userName},</p>
            <p>We are excited to inform you that you are eligible to receive the following devices: <strong>{$deviceList}</strong>.</p>
            <p>These devices are now being shipped to your location. You should receive them shortly.</p>
            <p>Thank you for your patience and trust in our services!</p>
            <p>Best Regards,</p>
            <p>Your Company Name</p>";
        return self::EmailBody($content);
    }

    public static function InviteEmailContent($userName, $link){
        $content = "          
            <p>Dear {$userName},</p>
            <p>You have been invited to join our company. Please click the link below to complete the registration process.</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Best Regards,</p>
            <p>Your Company Name</p>";
        return self::EmailBody($content);
    }


    public static function EmailBody($content) {
    
        $body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Device Shipment Notification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f7f9fc;
                    color: #333;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
    
                .email-content {
                    padding: 20px;
                    text-align: left;
                    line-height: 1.5;
                }
            </style>
        </head>
        <body>
            <div class='email-content'>$content</div>        
        </body>
        </html>";
    
        return $body;
    }
    

}