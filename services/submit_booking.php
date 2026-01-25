<?php
// services/submit_booking.php
define('IS_API', true);
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get JSON Input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

// CSRF Verification
$csrf_token = '';
if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'];
} else {
    $headers = getallheaders();
    $csrf_token = $headers['X-CSRF-Token'] ?? ($headers['X-Csrf-Token'] ?? '');
}

if (!verify_csrf_token($csrf_token)) {
    // Debug Logging
    error_log("CSRF FAILED: Received [" . ($csrf_token ? 'YES' : 'NO') . "] Expected [" . ($_SESSION['csrf_token'] ?? 'NONE') . "]");
    error_log("Session ID: " . session_id());

    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Security check failed. Please refresh the page.']);
    exit;
}

// Validation
$required = ['package_id', 'customer_name', 'email', 'phone', 'travel_date'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required field: ' . ucfirst(str_replace('_', ' ', $field))]);
        exit;
    }
}

// Moved inside try block for better error handling

try {
    // Data Sanitization
    $package_id = intval($input['package_id']);
    $customer_name = sanitize_input($input['customer_name']);
    $email = sanitize_input($input['email']);
    $phone = sanitize_input($input['phone']);
    $travel_date = sanitize_input($input['travel_date']);
    $duration = sanitize_input($input['duration'] ?? '');
    $adults = intval($input['adults'] ?? 1);
    $children = intval($input['children'] ?? 0);
    $hotel_category = sanitize_input($input['hotel_category'] ?? 'Mid-range');
    $interests = sanitize_input(is_array($input['interests'] ?? []) ? implode(', ', $input['interests']) : ($input['interests'] ?? ''));
    $special_requests = sanitize_input($input['special_requests'] ?? ''); // Optional

    // Determine Status: 'Pending'
    $status = 'Pending';
    $total_price = 0.00; // Will be calculated/confirmed by admin later or fetched from package

    // DB Init (Inside try to catch connection errors)
    $db = Database::getInstance();

    // Optional: Fetch package name/price for record keeping
    $pkg = $db->fetch("SELECT title, price FROM packages WHERE id = ?", [$package_id]);
    $package_name = $pkg ? $pkg['title'] : 'Unknown Package';
    $total_price = $pkg ? $pkg['price'] : 0.00; // Base price

    // Capture Affiliate ID
    $affiliate_id = null;
    if (isset($_SESSION['affiliate_id'])) {
        $affiliate_id = intval($_SESSION['affiliate_id']);
    } elseif (isset($_COOKIE['affiliate_id'])) {
        $affiliate_id = intval($_COOKIE['affiliate_id']);
    }

    // Insert Booking
    $sql = "INSERT INTO bookings (package_id, package_name, customer_name, email, phone, travel_date, duration, adults, children, hotel_category, interests, special_requests, total_price, status, affiliate_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $success = $db->execute($sql, [
        $package_id,
        $package_name,
        $customer_name,
        $email,
        $phone,
        $travel_date,
        $duration,
        $adults,
        $children,
        $hotel_category,
        $interests,
        $special_requests,
        $total_price,
        $status,
        $affiliate_id
    ]);

    if ($success) {
        $booking_id = $db->lastInsertId();

        // --- Notification Logic ---
        // 1. Send Admin Alert
        $adminData = [
            'Package' => $package_name,
            'Price' => 'Rs. ' . number_format($total_price, 2),
            'Customer Name' => $customer_name,
            'Email' => $email,
            'Phone' => $phone,
            'Travel Date' => $travel_date,
            'Special Requests' => $special_requests ?: 'None',
            'Booking ID' => '#' . $booking_id
        ];
        send_admin_notification_email("New Booking: $customer_name", $adminData, "View Booking", base_url("admin/booking-details.php?id=$booking_id"));

        // 2. Send Customer Confirmation
        $customerBookingDetails = [
            'Booking ID' => '#' . $booking_id,
            'Package' => $package_name,
            'Travel Date' => $travel_date,
            'Travelers' => "$adults Adults" . ($children > 0 ? ", $children Children" : ""),
            'Hotel Category' => $hotel_category,
            'Duration' => $duration ?: 'N/A',
            'Estimated Price' => ($total_price > 0) ? 'Rs. ' . number_format($total_price, 2) : 'Pending Quote'
        ];
        send_booking_confirmation_email($email, $customer_name, $customerBookingDetails);

        // Log it
        error_log("Booking Submitted: ID $booking_id");

        echo json_encode(['status' => 'success', 'message' => 'Your request has been submitted successfully! We will contact you shortly.', 'booking_id' => $booking_id]);
    } else {
        throw new Exception("Database insert failed.");
    }

} catch (Exception $e) {
    error_log("Booking Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error. Please try again later.']);
}
