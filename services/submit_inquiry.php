<?php
// services/submit_inquiry.php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

// Validation
if (empty($input['customer_name']) || empty($input['email']) || empty($input['phone'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Name, Email, and Phone are required.']);
    exit;
}

$name = sanitize_input($input['customer_name']);
$email = sanitize_input($input['email']);
$phone = sanitize_input($input['phone']);
$subject = sanitize_input($input['subject'] ?? 'General Inquiry');
$messageRaw = sanitize_input($input['special_requests'] ?? ''); // Map special_requests to message
$travelDate = sanitize_input($input['travel_date'] ?? 'Not Specified');

// Format complete message
$finalMessage = "Phone: $phone\nTravel Date: $travelDate\n\nMessage/Request:\n$messageRaw";

$db = Database::getInstance();

try {
    $sql = "INSERT INTO inquiries (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, datetime('now'))";
    $success = $db->execute($sql, [$name, $email, $subject, $finalMessage]);

    if ($success) {
        // Optional: Send Notification Email (Reuse logic if needed, skipping for concise file)
        echo json_encode(['status' => 'success', 'message' => 'Inquiry sent successfully! We will contact you soon.']);
    } else {
        throw new Exception("Database insert failed.");
    }
} catch (Exception $e) {
    error_log("Inquiry Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error.']);
}
?>