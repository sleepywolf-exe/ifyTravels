<?php
require_once __DIR__ . '/includes/db.php';
$db = Database::getInstance();
$destinations = $db->fetchAll("SELECT id, name, image_url, slug FROM destinations");
foreach ($destinations as $d) {
    echo "ID: " . $d['id'] . " | Name: " . $d['name'] . " | Slug: " . $d['slug'] . " | Image: " . $d['image_url'] . "\n";
}
?>