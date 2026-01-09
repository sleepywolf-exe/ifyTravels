<?php
// admin/auth_check.php - Authentication guard for admin pages
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin()) {
    redirect('login.php');
}
?>