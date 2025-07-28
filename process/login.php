<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $user = loginUser($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: apna-heaven.php');
        exit();
    }
}
?>