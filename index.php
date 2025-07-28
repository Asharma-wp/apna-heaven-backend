<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Redirect logic
if ($isLoggedIn) {
    // User is logged in - redirect to dashboard
    header('Location: dashboard.php');
} else {
    // User is not logged in - redirect to login page
    header('Location: apna-heaven.php');
}

// Ensure no further code is executed
exit();
?>