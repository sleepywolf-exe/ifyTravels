<?php
// services/subscribe.php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

try {
    $db = Database::getInstance();

    // Check if already subscribed
    $existing = $db->fetch("SELECT id FROM newsletter_subscribers WHERE email = ?", [$email]);

    if ($existing) {
        echo json_encode(['status' => 'success', 'message' => 'You are already subscribed!']);
        exit;
    }

    // Insert
    $db->execute("INSERT INTO newsletter_subscribers (email) VALUES (?)", [$email]);

    echo json_encode(['status' => 'success', 'message' => 'Successfully subscribed! Welcome to the club.']);
} catch (Exception $e) {
    error_log("Subscription error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
}
?>