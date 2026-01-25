<?php
require_once 'includes/db.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Checking 'bookings' table columns:\n";
$stmt = $pdo->query("DESCRIBE bookings");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

print_r($columns);

$required = ['duration', 'adults', 'children', 'hotel_category', 'interests', 'package_name'];
$missing = array_diff($required, $columns);

if (!empty($missing)) {
    echo "\n❌ MISSING COLUMNS: " . implode(', ', $missing) . "\n";
} else {
    echo "\n✅ All columns present.\n";
}
?>