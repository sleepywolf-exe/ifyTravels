<?php
require_once 'includes/db.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "Checking 'bookings' table schema...\n";

    // Helper to add column if not exists (SQLite/MySQL compatible logic where possible)
    // Detailed check needed because 'IF NOT EXISTS' in ALTER TABLE is not supported in all versions

    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

    $columnsToAdd = [
        'duration' => 'VARCHAR(50)',
        'adults' => 'INTEGER DEFAULT 1',
        'children' => 'INTEGER DEFAULT 0',
        'hotel_category' => 'VARCHAR(50)',
        'interests' => 'TEXT'
    ];

    foreach ($columnsToAdd as $col => $type) {
        try {
            // Try to select the column to see if it exists
            $db->fetch("SELECT $col FROM bookings LIMIT 1");
            echo "Column '$col' already exists.\n";
        } catch (Exception $e) {
            // Column likely doesn't exist, try adding it
            echo "Adding column '$col'...\n";
            try {
                $db->execute("ALTER TABLE bookings ADD COLUMN $col $type");
                echo "Column '$col' added successfully.\n";
            } catch (Exception $addErr) {
                echo "Error adding '$col': " . $addErr->getMessage() . "\n";
            }
        }
    }

    echo "Schema update complete.\n";

} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
