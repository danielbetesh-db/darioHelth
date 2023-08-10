<?php

include_once 'src/config.php'; 

// This script is for importing data from a json file to the database
$fileName = isset($argv[1]) ? $argv[1] : 'users_and_devices.json';
print "Importing users from $fileName \n";
ImportJsonDataToDB($pdo, $fileName);
 

