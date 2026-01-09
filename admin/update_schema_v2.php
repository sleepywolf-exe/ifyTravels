<?php
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();

$queries = [
    // Inquiries Table Updates
    "ALTER TABLE inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'new'",
    "ALTER TABLE inquiries ADD COLUMN admin_notes TEXT",
    "ALTER TABLE inquiries ADD COLUMN utm_source VARCHAR(255)",
    "ALTER TABLE inquiries ADD COLUMN utm_medium VARCHAR(255)",
    "ALTER TABLE inquiries ADD COLUMN utm_campaign VARCHAR(255)",

    // Bookings Table Updates
    "ALTER TABLE bookings ADD COLUMN utm_source VARCHAR(255)",
    "ALTER TABLE bookings ADD COLUMN utm_medium VARCHAR(255)",
    "ALTER TABLE bookings ADD COLUMN utm_campaign VARCHAR(255)"
];

echo "<h2>Starting Schema Update...</h2>";

foreach ($queries as $sql) {
    try {
        $db->execute($sql);
        echo "<p style='color: green;'>Success: " . htmlspecialchars($sql) . "</p>";
    } catch (Exception $e) {
        // Ignore "duplicate column" errors if re-run
        if (strpos($e->getMessage(), 'duplicate column') !== false) {
            echo "<p style='color: orange;'>Skipped (Exists): " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

echo "<h3>Update Complete.</h3>";
?>