<?php
require_once 'includes/db.php';
$db = Database::getInstance();
$tables = $db->fetchAll("SELECT name FROM sqlite_master WHERE type='table'");
echo "Tables in DB:\n";
foreach ($tables as $t) {
    echo "- " . $t['name'] . "\n";
}
?>