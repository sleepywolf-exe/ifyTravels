<?php
// admin/add_destination_field.php - Add destination_covered column

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "<h1>Adding 'destination_covered' Column</h1>";

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM packages LIKE 'destination_covered'");
    if ($stmt->fetch()) {
        echo "<p style='color:orange'>Column 'destination_covered' already exists.</p>";
    } else {
        // Add column
        $sql = "ALTER TABLE packages ADD COLUMN destination_covered VARCHAR(255) DEFAULT NULL AFTER destination_id";
        $pdo->exec($sql);
        echo "<p style='color:green'>Successfully added 'destination_covered' column to 'packages' table.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='packages.php'>Go Back to Packages</a>";
?>