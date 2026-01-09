<?php
// services/db_fix_cloud.php
// Run this script once to fix Database Schema on Cloud/MySQL

require_once __DIR__ . '/../includes/db.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "<h2>Database Schema Fixer</h2>";
echo "<pre>";

function addColumnIfNeeded($pdo, $table, $column, $definition)
{
    try {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        $exists = $stmt->fetch();

        if (!$exists) {
            $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
            $pdo->exec($sql);
            echo "✅ Added column `$column` to table `$table`.\n";
        } else {
            echo "ℹ️ Column `$column` already exists in table `$table`.\n";
        }
    } catch (Exception $e) {
        echo "⚠️ Error adding `$column`: " . $e->getMessage() . "\n";
    }
}

function makeColumnNullable($pdo, $table, $column, $definition)
{
    try {
        $sql = "ALTER TABLE `$table` CHANGE `$column` `$column` $definition";
        $pdo->exec($sql);
        echo "✅ Modified column `$column` in table `$table` to be nullable/compatible.\n";
    } catch (Exception $e) {
        echo "⚠️ Error modifying `$column`: " . $e->getMessage() . "\n";
    }
}

// 1. Fix 'bookings' table
echo "\n--- Checking 'bookings' table ---\n";
addColumnIfNeeded($pdo, 'bookings', 'created_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
addColumnIfNeeded($pdo, 'bookings', 'status', "VARCHAR(50) DEFAULT 'Pending'");
addColumnIfNeeded($pdo, 'bookings', 'package_name', "VARCHAR(255) NULL");
// Ensure package_id can be NULL for generic inquiries
makeColumnNullable($pdo, 'bookings', 'package_id', "INT(11) NULL");

// 2. Fix 'inquiries' table
echo "\n--- Checking 'inquiries' table ---\n";
addColumnIfNeeded($pdo, 'inquiries', 'created_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
addColumnIfNeeded($pdo, 'inquiries', 'status', "VARCHAR(50) DEFAULT 'New'");
addColumnIfNeeded($pdo, 'inquiries', 'phone', "VARCHAR(50) NULL");

// 3. Fix 'packages' table (for previous changes)
echo "\n--- Checking 'packages' table ---\n";
addColumnIfNeeded($pdo, 'packages', 'destination_covered', "VARCHAR(255) NULL");
addColumnIfNeeded($pdo, 'packages', 'trust_badges', "TEXT NULL");
addColumnIfNeeded($pdo, 'packages', 'is_new', "TINYINT(1) DEFAULT 0");

echo "\n--- Required Tables Check ---\n";
$tables = ['site_settings', 'users', 'destinations', 'packages', 'bookings', 'inquiries'];
foreach ($tables as $t) {
    echo "Checking $t... ";
    try {
        $pdo->query("SELECT 1 FROM $t LIMIT 1");
        echo "OK\n";
    } catch (Exception $e) {
        echo "MISSING or Error (" . $e->getMessage() . ")\n";
    }
}

echo "\nDone. Delete this file after successful execution if desired.";
echo "</pre>";
?>