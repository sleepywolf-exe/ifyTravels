<?php
// admin/booking_actions.php
require 'auth_check.php';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check (Assuming csrf_token() validation exists or implementing basic check if not)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Validation Failed");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        // --- CREATE NEW BOOKING ---
        $customer_name = sanitize_input($_POST['customer_name']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $travel_date = sanitize_input($_POST['travel_date']);
        $package_id = !empty($_POST['package_id']) ? intval($_POST['package_id']) : null;
        $package_name = sanitize_input($_POST['package_name'] ?: 'Custom Booking');
        $total_price = floatval($_POST['total_price']);
        $status = sanitize_input($_POST['status']);
        $special_requests = sanitize_input($_POST['special_requests']);

        try {
            $sql = "INSERT INTO bookings (package_id, package_name, customer_name, email, phone, travel_date, total_price, status, special_requests, created_at, utm_source) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin_manual')";

            // Use PHP Date for consistency/MySQL compatibility
            $created_at = date('Y-m-d H:i:s');

            $db->execute($sql, [
                $package_id,
                $package_name,
                $customer_name,
                $email,
                $phone,
                $travel_date,
                $total_price,
                $status,
                $special_requests,
                $created_at
            ]);

            $booking_id = $db->lastInsertId();

            // Send Email Notification (Optional for manual entry, but good for record)
            // send_lead_confirmation_email($email, $customer_name, $phone); 

            header("Location: bookings.php?msg=created");
        } catch (Exception $e) {
            die("Error creating booking: " . $e->getMessage());
        }

    } elseif ($action === 'update') {
        // --- UPDATE EXISTING BOOKING ---
        // (If needed later, for now we likely just need Create)
    } elseif ($action === 'delete') {
        // --- DELETE BOOKING ---
        $id = intval($_POST['id']);
        try {
            $db->execute("DELETE FROM bookings WHERE id = ?", [$id]);
            header("Location: bookings.php?msg=deleted");
        } catch (Exception $e) {
            die("Error deleting booking: " . $e->getMessage());
        }
    }
}
?>