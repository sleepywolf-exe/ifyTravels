<?php
require 'auth_check.php';
// require_once __DIR__ . '/../includes/functions.php'; // Included by auth_check -> functions
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Checking schema...<br>";

try {
    // Try to select the column to see if it exists
    $pdo->query("SELECT admin_notes FROM bookings LIMIT 1");
    echo "Column 'admin_notes' already exists.";
} catch (Exception $e) {
    // Column doesn't exist, add it
    echo "Column 'admin_notes' does not exist. Adding it...<br>";
    try {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN admin_notes TEXT DEFAULT NULL");
        echo "Successfully added 'admin_notes' column.";
    } catch (Exception $e2) {
        echo "Error adding column: " . $e2->getMessage();
    }
}
?>