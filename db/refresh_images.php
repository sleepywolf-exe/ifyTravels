<?php
// db/refresh_images.php
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();

echo "Refreshing Images...\n";

// Map: Destination Slug => New Image URL
$destImages = [
    'maldives' => 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800&fit=crop',
    'dubai' => 'https://images.unsplash.com/photo-1518684079-3c830dcefadd?w=800&fit=crop',
    'paris' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&fit=crop',
    'switzerland' => 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=800&fit=crop',
    'thailand' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800&fit=crop',
    'bali' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&fit=crop',
    'singapore' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&fit=crop',
];

foreach ($destImages as $slug => $url) {
    // Check if dest exists
    $exists = $db->fetch("SELECT id FROM destinations WHERE slug LIKE ?", ["%$slug%"]);
    if ($exists) {
        $db->execute("UPDATE destinations SET image_url = ? WHERE id = ?", [$url, $exists['id']]);
        echo "Updated Destination: {$slug}\n";
    }
}

// Update Packages (Just set a few generic nice ones for popular packages)
$pkgImages = [
    'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?w=800&fit=crop', // Hotel/Pool
    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&fit=crop', // Resort
    'https://images.unsplash.com/photo-1571896349842-6e53ce41cd63?w=800&fit=crop', // Room
];

// Get all packages
$packages = $db->fetchAll("SELECT id FROM packages");
foreach ($packages as $i => $pkg) {
    $url = $pkgImages[$i % count($pkgImages)];
    $db->execute("UPDATE packages SET image_url = ? WHERE id = ?", [$url, $pkg['id']]);
}
echo "Updated " . count($packages) . " Packages.\n";
echo "Done!\n";
