<?php
// services/check_schema.php
require_once __DIR__ . '/../includes/db.php';
$db = Database::getInstance();
$columns = $db->fetchAll("SHOW COLUMNS FROM bookings");
echo "<pre>";
print_r($columns);
echo "</pre>";
?>