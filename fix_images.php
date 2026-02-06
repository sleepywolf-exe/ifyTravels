<?php
// fix_images.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

$fixes = [
    // [slug, correct_image_path]
    'ultimate-guide-international-tour-packages-2026' => 'assets/images/destinations/georgia.jpg', // Replaces switzerland.jpg
    'travel-guide-london' => 'assets/images/destinations/paris.png', // Replaces london.jpg
    'budget-vs-luxury-vacation-deals' => 'assets/images/destinations/dubai.jpg',
    'visa-guide-indian-travelers-2026' => 'assets/images/destinations/paris.png', // Replaces london.jpg
    'incredible-india-domestic-tour-packages' => 'assets/images/destinations/kerala.png',
    'top-10-romantic-honeymoon-packages' => 'assets/images/destinations/maldives.jpg'
];

foreach ($fixes as $slug => $img) {
    echo "Updating $slug -> $img ... ";
    $sql = "UPDATE posts SET image_url = ? WHERE slug = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$img, $slug]);
    echo "Done.<br>";
}

echo "Image path fixes complete.";
