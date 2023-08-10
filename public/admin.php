<?php 

require_once '../src/config.php';

$userManager = new UserManager($pdo);
$notApprovedUsers = $userManager->getNotApprovedUsers();
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="wrapper"> 
        <div style="display:flex; justify-content:center; align-items:center;">
            <div>
                <div>
                    <div>Not approved users count: <?php echo count($notApprovedUsers); ?></div>
                    <div style="font-size:14px; padding: 5px 0;">Send Email to all users that didnt approved the email</div>
                    <button id="sendEmailBtn">Send Emails</button>
                    <div style="white-space:pre-line; padding:5px; line-height: 1.5;" id="sendMessageResult"></div>
                </div>
                <a href="example.json" download>Download template file</a>
                <form id="jsonUploadForm" class="json_input" method="post" enctype="multipart/form-data">
                    Upload a JSON file to database:
                    <input type="file" name="file" accept=".json" required>
                    <br><br>
                    <input type="submit" value="Upload JSON">
                </form>
                <div id="loaderArea"></div>
                <div style="padding:5px 0;">Error log:</div>
                <div style="" id="ImportLogArea"></div>
            </div>
        </div>
    </div>
    <script src="js/api.js"></script>
    <script src="js/main.js"></script>
</body>
</html>         