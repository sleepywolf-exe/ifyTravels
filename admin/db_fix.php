<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';

echo "<html><body style='font-family: sans-serif; padding: 2rem;'>";
echo "<h1>Database Schema Repair</h1>";
echo "<p>Attempting to fix 'inquiries' table structure...</p>";

$db = Database::getInstance();
$pdo = $db->getConnection();

$commands = [
    "ALTER TABLE inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'new'",
    "ALTER TABLE inquiries ADD COLUMN admin_notes TEXT",
    "ALTER TABLE inquiries ADD COLUMN utm_source VARCHAR(255)",
    "ALTER TABLE inquiries ADD COLUMN utm_medium VARCHAR(255)",
    "ALTER TABLE inquiries ADD COLUMN utm_campaign VARCHAR(255)"
];

foreach ($commands as $sql) {
    echo "<div style='margin-bottom: 10px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd;'>";
    echo "<strong>Running:</strong> <code>$sql</code><br>";
    try {
        $pdo->exec($sql);
        echo "<span style='color: green; font-weight: bold;'>✅ Success</span>";
    } catch (PDOException $e) {
        // Check if error is "Duplicate column name" (Error 1060 or SQLState 42S21)
        if (strpos($e->getMessage(), 'Duplicate column') !== false || $e->getCode() == '42S21') {
            echo "<span style='color: blue; font-weight: bold;'>ℹ️ Column already exists (Skipped)</span>";
        } else {
            echo "<span style='color: red; font-weight: bold;'>❌ Error:</span> " . $e->getMessage();
        }
    }
    echo "</div>";
}

echo "<h3>✅ Repair Complete</h3>";
echo "<p><a href='inquiries.php' style='background: #0A6CF1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go Back to Inquiries</a></p>";
echo "</body></html>";
?>