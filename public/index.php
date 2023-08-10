<?php

require_once '../src/config.php';

if(!isset($_GET['id'])) {
    echo 'No user id provided';
    exit;
}
// For testing purpose, uncomment the line below
// $_SESSION['user'] = null;

$userId = $_GET['id'];
// using session for friendly user experience,
// and reduce the number of requests to the database
$isIdentified = isset($_SESSION['user']) && $_SESSION['user']->uniqId === $userId;
$user = null;
if(!$isIdentified) {
    $userManager = new UserManager($pdo);
    $user = $userManager->getUserWithDevices($userId);
     if(!$user) {
        echo 'User not found';
        exit;
    }
}else{
    $user = $_SESSION['user'];
}

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
        <?php if(!$isIdentified) : ?>
            <h2 style="text-align:center;"><?php echo "Hi " . $user->userName . " please fill the form below to continue"; ?></h2>
            <form id="lp_form" action="api/user.php" method="post">
                <label for="userId">User ID:</label><br>
                <input type="text" id="userId" value="<?php /* echo $user->userId; */ ?>" name="userId" required><br><br>

                <label for="userEmail">User Email:</label><br>
                <input type="email" id="userEmail" value="<?php /*echo $user->userEmail; */ ?>" name="userEmail" required><br><br>

                <input type="submit" value="Submit">
                <div id="errorField"></div>
                <div id="loaderArea"></div>
            </form>
            <script src="js/api.js"></script>
            <script src="js/main.js"></script>
        <?php else: ?> 
            <?php echo HtmlTemplate::UserDeviceView($user); ?> 
         <?php endif; ?>
    </div>
    
</body>
</html>

 
