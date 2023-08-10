<?php
 
require_once __DIR__.'/../vendor/autoload.php'; 
include_once __DIR__.'/../src/core/user-manager.php';
include_once __DIR__.'/../src/core/html-template.php';
include_once __DIR__.'/../src/core/send-email.php';
include_once __DIR__.'/../src/core/import-file-stream.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

//Database credentials
$host = 'localhost';
$db   = 'dario';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}