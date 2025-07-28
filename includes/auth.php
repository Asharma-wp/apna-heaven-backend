<?php
function registerUser($userData) {
    global $pdo;
    
    try {
        // Check if passwords match
        if ($userData['password'] !== $userData['confirm_password']) {
            throw new Exception("Passwords do not match");
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$userData['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Email already registered");
        }
        
        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, company_name, phone_number, email, address, password, deal_in, transaction_type) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userData['full_name'],
            $userData['company_name'],
            $userData['phone_number'],
            $userData['email'],
            $userData['address'],
            $hashedPassword,
            $userData['deal_in'],
            $userData['transaction_type']
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Insert property preferences
        if (!empty($userData['property_types'])) {
            $stmt = $pdo->prepare("INSERT INTO user_property_preferences (user_id, property_category, property_type) 
                                  VALUES (?, ?, ?)");
            
            foreach ($userData['property_types'] as $type) {
                $stmt->execute([$userId, $userData['property_category'], $type]);
            }
        }
        
        return $userId;
    } catch (Exception $e) {
        throw $e;
    }
}

function loginUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}
?>