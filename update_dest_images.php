<?php
require_once __DIR__ . '/includes/db.php';
$db = Database::getInstance();

$updates = [
    'bali' => 'assets/images/destinations/bali.png',
    'maldives' => 'assets/images/destinations/maldives.png',
    'kerala' => 'assets/images/destinations/kerala.png',
    'dubai' => 'assets/images/destinations/dubai.png',
    'goa' => 'assets/images/destinations/goa.png',
    'manali' => 'assets/images/destinations/manali.png',
    'shimla' => 'assets/images/destinations/shimla.png',
    'paris' => 'assets/images/destinations/paris.png'
];

foreach ($updates as $slug => $image) {
    // Check if file exists first to be safe
    if (file_exists(__DIR__ . '/' . $image)) {
        $db->query("UPDATE destinations SET image_url = ? WHERE slug = ?", [$image, $slug]);
        echo "Updated $slug to $image\n";
    } else {
        echo "Skipped $slug: File not found ($image)\n";
    }
}
echo "Database update complete.\n";
?>