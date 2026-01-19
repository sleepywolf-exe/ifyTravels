<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['partner_logged_in']) || $_SESSION['partner_logged_in'] !== true) {
    header("Location: " . base_url('partner/login.php'));
    exit;
}

$partnerId = $_SESSION['partner_id'];
