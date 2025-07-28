<?php
// admin/admin-auth.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php"); // Redirect to main site
    exit();
}
?>