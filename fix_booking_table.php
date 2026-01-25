<?php
// fix_booking_table.php
// Run this file once to ensure the database has all necessary columns for the new booking system.

require_once 'includes/functions.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "<h1>Database Schema Repair</h1>";

$table = 'bookings';
$requiredColumns = [
    'package_name' => "VARCHAR(255) NULL AFTER package_id",
    'duration' => "VARCHAR(50) NULL AFTER travel_date",
    'adults' => "INT DEFAULT 1 AFTER duration",
    'children' => "INT DEFAULT 0 AFTER adults",
    'hotel_category' => "VARCHAR(50) DEFAULT 'Mid-range' AFTER children",
    'interests' => "TEXT NULL AFTER hotel_category",
    'special_requests' => "TEXT NULL AFTER interests",
    'total_price' => "DECIMAL(10,2) DEFAULT 0.00 AFTER special_requests",
    'status' => "VARCHAR(50) DEFAULT 'Pending' AFTER total_price",
    'affiliate_id' => "INT NULL AFTER status"
];

try {
    // 1. Get current columns
    $stmt = $pdo->query("DESCRIBE $table");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<p>Checking table <strong>$table</strong>...</p>";
    echo "<ul>";

    foreach ($requiredColumns as $col => $def) {
        if (!in_array($col, $existing)) {
            echo "<li style='color: red;'>Missing column: <strong>$col</strong>. Adding...</li>";

            // Add Column
            $sql = "ALTER TABLE $table ADD COLUMN $col $def";
            $pdo->exec($sql);

            echo "<li>✅ Added $col</li>";
        } else {
            echo "<li style='color: green;'>Column <strong>$col</strong> exists. OK.</li>";
        }
    }
    echo "</ul>";

    echo "<h3>✅ Database Check Complete.</h3>";
    echo "<p>You can now delete this file from your server.</p>";

} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>