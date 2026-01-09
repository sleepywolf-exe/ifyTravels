<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/data/loader.php';

echo "<h1>System Diagnostic</h1>";

// 1. Check DB Schema
echo "<h2>1. Database Schema Check</h2>";
try {
    $db = Database::getInstance();
    $stmt = $db->getConnection()->query("SHOW COLUMNS FROM packages");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Files in 'packages' table: " . implode(', ', $columns) . "<br>";

    if (in_array('trust_badges', $columns)) {
        echo "<span style='color:green'>✅ 'trust_badges' column exists.</span><br>";
    } else {
        echo "<span style='color:red'>❌ 'trust_badges' column MISSING.</span><br>";
        // Attempt to Add
        try {
            $db->getConnection()->exec("ALTER TABLE packages ADD COLUMN trust_badges TEXT DEFAULT NULL");
            echo "<span style='color:green'>✅ Automatically fixed: 'trust_badges' column added.</span><br>";
        } catch (Exception $e) {
            echo "<span style='color:red'>❌ Failed to add column: " . $e->getMessage() . "</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span style='color:red'>CRITICAL DB ERROR: " . $e->getMessage() . "</span>";
}

// 2. Test loadPackages()
echo "<h2>2. Data Loading Test</h2>";
try {
    $packages = loadPackages();
    echo "loadPackages() returned: " . gettype($packages) . "<br>";
    echo "Count: " . (is_array($packages) ? count($packages) : 'N/A') . "<br>";

    if (is_array($packages) && count($packages) > 0) {
        $first = reset($packages);
        echo "First Package Keys: " . implode(', ', array_keys($first)) . "<br>";
        echo "Trust Badges Type: " . gettype($first['trust_badges']) . "<br>";
        echo "Trust Badges Value: " . json_encode($first['trust_badges']) . "<br>";
    }
} catch (Throwable $t) {
    echo "<span style='color:red'>CRITICAL LOAD ERROR: " . $t->getMessage() . " in " . $t->getFile() . " on line " . $t->getLine() . "</span>";
}
?>