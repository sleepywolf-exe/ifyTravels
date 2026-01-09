<?php
// admin/index.php
// Handles /admin/ requests: proper redirection based on login status

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // User is logged in, go to dashboard
    header('Location: dashboard.php');
    exit;
} else {
    // User is NOT logged in, go to login page
    header('Location: login.php');
    exit;
}
?>