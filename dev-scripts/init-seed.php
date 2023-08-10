<?php
include_once 'src/config.php';

/**
 * Create the tables if they don't exist
 */
function createTablesIfNotExist($pdo){
    try { 
        $sql = " 
        CREATE TABLE IF NOT EXISTS users (
            uniqId VARCHAR(255) PRIMARY KEY,
            userName VARCHAR(255) NOT NULL,
            userEmail VARCHAR(255) NOT NULL UNIQUE,
            userPhone VARCHAR(50) NOT NULL,
            userId VARCHAR(255) NOT NULL UNIQUE,
            approved TINYINT(1) DEFAULT 0
        );  
    
        CREATE TABLE IF NOT EXISTS devices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            deviceName VARCHAR(255) NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS user_devices (
            userUniqId VARCHAR(255),
            deviceId INT,
            FOREIGN KEY (userUniqId) REFERENCES users(uniqId) ON DELETE CASCADE,
            FOREIGN KEY (deviceId) REFERENCES devices(id) ON DELETE CASCADE,
            PRIMARY KEY (userUniqId, deviceId)
        );
        ";
        $pdo->exec($sql);    
    
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
    
}

/**
 * Seed the database with 15 devices
 */
function seedDevices($pdo) {
    for ($i = 1; $i <= 15; $i++) {
        $deviceName = "Device" . $i;

        $stmt = $pdo->prepare("SELECT 1 FROM devices WHERE deviceName = ?");
        $stmt->execute([$deviceName]);
 
        if ($stmt->rowCount() === 0) {
            $insertStmt = $pdo->prepare("INSERT INTO devices (deviceName) VALUES (?)");
            $insertStmt->execute([$deviceName]);
        }
    }
}

/**
 * Initialize the database instance, used for testing purposes
 */
function InitInstance($pdo) {  
    $pdo->exec("SET foreign_key_checks = 0");
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $table = $row[0];
        $pdo->exec("DROP TABLE IF EXISTS $table");
    }
    $pdo->exec("SET foreign_key_checks = 1");
}
 
InitInstance($pdo);
createTablesIfNotExist($pdo);
seedDevices($pdo);