<?php
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset partner session vars
unset($_SESSION['partner_logged_in']);
unset($_SESSION['partner_id']);
unset($_SESSION['partner_name']);

// Destroy if no admin session exists (optional, but safer to just keep session alive if mixed? No, let's keep it simple)
// actually we might have admin session too. So just unset specific keys.

header("Location: " . base_url('partner/login.php'));
exit;
