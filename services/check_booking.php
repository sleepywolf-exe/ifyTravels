<?php
require_once __DIR__ . '/../includes/db.php';
$id = $_GET['id'] ?? 1;
$db = Database::getInstance();
$booking = $db->fetch("SELECT * FROM bookings WHERE id = ?", [$id]);
echo "<pre>";
print_r($booking);
echo "</pre>";
?>