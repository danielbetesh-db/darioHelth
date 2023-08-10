<?php


class UserManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getUserById($id, $format = 'object') {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE userId = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        if($result === false) {
            return false;
        }

        if ($format === 'json') {
            return json_encode($result);
        }

        return (object) $result;
    }

    public function getUserByUniqId($uniqId, $format = 'object') {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE uniqId = :id");
        $stmt->execute(['id' => $uniqId]);
        $result = $stmt->fetch();

        if($result === false) {
            return false;
        }
        
        if ($format === 'json') {
            return json_encode($result);
        }

        return (object) $result;
    }

    public function setApproved($uniqId, $approved) {
        $stmt = $this->pdo->prepare("UPDATE users SET approved = :approved WHERE uniqId = :uniqId");
        $stmt->execute(['uniqId' => $uniqId, 'approved' => $approved]);
    }

    public function getNotApprovedUsers($format = 'object') {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE approved = 0");
        $results = $stmt->fetchAll();

        if($results === false) {
            return false;
        }

        if ($format === 'json') {
            return json_encode($results);
        }

        return array_map(function ($item) {
            return (object) $item;
        }, $results);
    }

    public function getAllUserWithDevices($format = 'object') {
        $stmt = $this->pdo->prepare("
            SELECT u.*, d.deviceName 
            FROM users u 
            JOIN user_devices ud ON u.uniqId = ud.userUniqId 
            JOIN devices d ON ud.deviceId = d.id
        ");
    
        $stmt->execute();
        $results = $stmt->fetchAll();
    
        $users = [];
        foreach ($results as $row) {
            $uniqId = $row['uniqId'];
            if (!isset($users[$uniqId])) {
                $users[$uniqId] = [
                    'uniqId' => $row['uniqId'],
                    'userName' => $row['userName'],
                    'userEmail' => $row['userEmail'],
                    'userPhone' => $row['userPhone'],
                    'userId' => $row['userId'],
                    'approved' => $row['approved'],
                    'devices' => []
                ];
            }
    
            $users[$uniqId]['devices'][] = $row['deviceName'];
        }
    
        $users = array_values($users); 
    
        if ($format === 'json') {
            return json_encode($users);
        }
    
        return array_map(function ($item) {
            return (object) $item;
        }, $users);
    }
    

    public function getUserWithDevices($uniqId, $format = 'object') {
        $stmt = $this->pdo->prepare("SELECT u.*, d.deviceName FROM users u 
            JOIN user_devices ud ON u.uniqId = ud.userUniqId 
            JOIN devices d ON ud.deviceId = d.id 
            WHERE u.uniqId = :id");

        $stmt->execute(['id' => $uniqId]);
        $results = $stmt->fetchAll();
        
        if($stmt->rowCount() === 0) {
            return false;
        }
        $user = null;
        $devices = [];
        foreach ($results as $row) {
            if (!$user) {
                $user = [
                    'uniqId' => $row['uniqId'],
                    'userName' => $row['userName'],
                    'userEmail' => $row['userEmail'],
                    'userPhone' => $row['userPhone'],
                    'userId' => $row['userId'],
                    'approved' => $row['approved'],
                    'devices' => []
                ];
            }
            $devices[] = $row['deviceName'];
        }

        if ($user) {
            $user['devices'] = $devices;
        }

        if ($format === 'json') {
            return json_encode($user);
        }

        return (object) $user;
    }

    //Used for import file
    public function updateUserData($user, $deviceIds){
        //Validate user and device
        $errors = $this->validateUserAndDevice($user, $deviceIds);
        if(!empty($errors)){
            print "User validation failed. Skipping user insertion. \n";
            var_dump($errors);
            $this->currentObject = [];
            $this->deviceIds = [];
            return;
        }

        $stmt = $this->pdo->prepare("SELECT uniqId FROM users WHERE userEmail = :userEmail OR userId = :userId");
        $stmt->execute([
            'userEmail' => $user['userEmail'],
            'userId' => $user['userId']
        ]);

        if ($stmt->fetch()) {
            print "User " . $user['userId'] . " already exists. Skipping device insertion. \n";
        } else {
            // Insert user if not exist
            $user['uniqId'] = uniqid("user");
            $stmt = $this->pdo->prepare("INSERT INTO users (uniqId, userName, userEmail, userPhone, userId) VALUES (:uniqId, :userName, :userEmail, :userPhone, :userId)");
            $stmt->execute($user);

            foreach ($deviceIds as $deviceId) {
                // Validate if device id exists
                $stmt = $this->pdo->prepare("SELECT id FROM devices WHERE id = :deviceId");
                $stmt->execute(['deviceId' => $deviceId]);

                if ($stmt->fetch()) {
                    // Insert user device relationship if device exists
                    $stmt = $this->pdo->prepare("INSERT IGNORE INTO user_devices (userUniqId, deviceId) VALUES (:userUniqId, :deviceId)");
                    $stmt->execute([
                        'userUniqId' => $user['uniqId'],
                        'deviceId' => $deviceId
                    ]);
                } else {
                    print "Device ID $deviceId does not exist and will be skipped. \n";
                }
            }
        }
    }

    // Validate user and device
    private function validateUserAndDevice($user, $deviceIds) {
        $errors = []; 
        
        // userName validation
        if (!isset($user['userName']) || empty($user['userName'])) {
            $errors[] = 'Invalid userName';
        }
    
        // userEmail validation
        $emailPattern = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
        if (!isset($user['userEmail']) || !preg_match($emailPattern, $user['userEmail'])) {
            $errors[] = 'Invalid userEmail';
        }
    
        // userId validation
        if (!isset($user['userId']) || !is_numeric($user['userId'])) {
            $errors[] = 'Invalid userId';
        }
    
        // deviceId validation
        if (!is_array($deviceIds)) {
            $errors[] = 'Invalid deviceId';
        }
    
        return $errors;
    }
    
    
}