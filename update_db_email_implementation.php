<?php
// update_db_email_implementation.php
// Run this file to apply database changes for the Email & Password Reset implementation.

require_once __DIR__ . '/includes/functions.php';

// Enable error reporting for this script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Update: Email Implementation</h1>";
echo "<pre>";

$db = Database::getInstance();

try {
    echo "Checking 'affiliates' table schema...\n";

    // 1. Check for reset_token
    echo "1. Checking for 'reset_token' column... ";
    $columns = $db->fetchAll("SHOW COLUMNS FROM affiliates LIKE 'reset_token'");

    if (empty($columns)) {
        echo "Missing. Adding...\n";
        $sql = "ALTER TABLE affiliates ADD COLUMN reset_token VARCHAR(64) NULL DEFAULT NULL AFTER password_hash";
        $db->execute($sql);
        echo "   -> 'reset_token' added.\n";
    } else {
        echo "Exists. Skipped.\n";
    }

    // 2. Check for reset_expiry
    echo "2. Checking for 'reset_expiry' column... ";
    $columns = $db->fetchAll("SHOW COLUMNS FROM affiliates LIKE 'reset_expiry'");

    if (empty($columns)) {
        echo "Missing. Adding...\n";
        $sql = "ALTER TABLE affiliates ADD COLUMN reset_expiry DATETIME NULL DEFAULT NULL AFTER reset_token";
        $db->execute($sql);
        echo "   -> 'reset_expiry' added.\n";
    } else {
        echo "Exists. Skipped.\n";
    }

    echo "\n---------------------------------------------------\n";
    echo "SUCCESS! Database is up to date for Email features.\n";
    echo "---------------------------------------------------\n";

} catch (Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>