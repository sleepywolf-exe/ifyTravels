<?php
// Redirect old destination URLs to new slug-based URLs
include __DIR__ . '/includes/functions.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . base_url('/destinations'));
    exit;
}

// Fetch destination by ID to get slug
$db = Database::getInstance();
$destination = $db->fetch("SELECT slug FROM destinations WHERE id = ?", [$id]);

if (!$destination || empty($destination['slug'])) {
    header('Location: ' . base_url('/destinations'));
    exit;
}

// 301 Permanent Redirect to slug-based URL
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . destination_url($destination['slug']));
exit;
