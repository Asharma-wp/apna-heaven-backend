<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    try {
        $userData = [
            'full_name' => $_POST['full_name'],
            'company_name' => $_POST['company_name'],
            'phone_number' => $_POST['phone_number'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password'],
            'address' => $_POST['address'],
            'deal_in' => $_POST['deal_in'],
            'transaction_type' => $_POST['transaction_type'],
            'property_category' => $_POST['property_category'],
            'property_types' => $_POST['property_types'] ?? []
        ];
        
        $userId = registerUser($userData);
        
        // Store data in session
        $_SESSION['registration_success'] = true;
        $_SESSION['new_user_id'] = $userId;
        $_SESSION['success'] = 'Registration successful! Please login.';
        
        // Don't redirect - let the form stay on the same page
        // header('Location: apna-heaven.php');
        // exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>