<?php

/**
 * I used this script to generate a JSON file with massive number of users and devices.
 * The file is used to import data into the database.
 * format :
 * [
    * {
        * "userName": "user1",
        * "userEmail": "email1@email.com",
        * "userPhone": "555-123-4567",
        * "userId": "300000001",
        * "devices": [
            * {
                * "deviceId": 1
            * }
        * ]
    * }
 * ]
 */
function generateUserData($index) {
    $userName = generateRandomString(8);
    $userEmail = generateRandomString(10) . "@" . generateRandomString(10) .".com";
    $userPhone = '555-' . rand(100, 999) . '-' . rand(1000, 9999);
    $userId =   300000000 + $index; 

    $devices = [];
    // Pick 1 to 4 random devices
    $numDevices = rand(1, 4);
    $assignedDevices = [];

    for ($j = 0; $j < $numDevices; $j++) {
        do {
            $randomDeviceId = rand(1, 15);
        } while (in_array($randomDeviceId, $assignedDevices));

        $assignedDevices[] = $randomDeviceId;
        $devices[] = ['deviceId' => $randomDeviceId];
    }

    return [
        'userName' => $userName,
        'userEmail' => $userEmail,
        'userPhone' => $userPhone,
        'userId' => $userId,
        'devices' => $devices
    ];
}
 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Write the data to a json file
$file = fopen('users_and_devices.json', 'w');
fwrite($file, '[');  

$numUsers = isset($argv[1]) ? $argv[1] : 100;
for ($i = 0; $i < $numUsers; $i++) {
    $userData = generateUserData($i);
    print "Row $i \n";
    fwrite($file, json_encode($userData));
 
    if ($i < $numUsers - 1) {
        fwrite($file, ',');
    }
}

fwrite($file, ']'); 
fclose($file);
