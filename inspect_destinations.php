<?php
require_once 'includes/db.php';
$db = Database::getInstance();
$destinations = $db->fetchAll("SELECT id, name, slug, image_url, description FROM destinations");
echo "ID | Name | Slug | Image URL | Description\n";
echo str_repeat("-", 80) . "\n";
foreach ($destinations as $d) {
    echo "{$d['id']} | {$d['name']} | {$d['slug']} | {$d['image_url']} | " . substr($d['description'], 0, 30) . "...\n";
}
