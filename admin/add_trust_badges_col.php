<?php
require_once __DIR__ . '/../includes/functions.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Checking for trust_badges column...\n";

try {
    // Try to select the column
    $pdo->query("SELECT trust_badges FROM packages LIMIT 1");
    echo "Column 'trust_badges' already exists.\n";
} catch (Exception $e) {
    // Column doesn't exist, add it
    echo "Column 'trust_badges' does not exist. Adding it...\n";
    try {
        $pdo->exec("ALTER TABLE packages ADD COLUMN trust_badges TEXT DEFAULT NULL");
        echo "Successfully added 'trust_badges' column.\n";
    } catch (Exception $e2) {
        echo "Error adding column: " . $e2->getMessage() . "\n";
    }
}
?>