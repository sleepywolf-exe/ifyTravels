<?php
// Redirect old package URLs to new slug-based URLs
include __DIR__ . '/includes/functions.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . base_url('/packages'));
    exit;
}

// Fetch package by ID to get slug
$db = Database::getInstance();
$package = $db->fetch("SELECT slug FROM packages WHERE id = ?", [$id]);

if (!$package || empty($package['slug'])) {
    header('Location: ' . base_url('/packages'));
    exit;
}

// 301 Permanent Redirect to slug-based URL
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . package_url($package['slug']));
exit;
