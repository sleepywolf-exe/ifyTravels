<?php
// fix_db_images.php
// Run this file in your browser: http://localhost/ifyTravels/fix_db_images.php (or your equivalent URL)

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $fixes = [
        // [slug, correct_image_path]
        'ultimate-guide-international-tour-packages-2026' => 'assets/images/destinations/georgia.jpg',
        'travel-guide-london' => 'assets/images/destinations/paris.png',
        'budget-vs-luxury-vacation-deals' => 'assets/images/destinations/dubai.jpg',
        'visa-guide-indian-travelers-2026' => 'assets/images/destinations/paris.png',
        'incredible-india-domestic-tour-packages' => 'assets/images/destinations/kerala.png',
        'top-10-romantic-honeymoon-packages' => 'assets/images/destinations/maldives.jpg'
    ];

    echo "<h1>Fixing Blog Images...</h1>";
    echo "<ul>";

    foreach ($fixes as $slug => $img) {
        $sql = "UPDATE posts SET image_url = ? WHERE slug = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$img, $slug]);

        if ($stmt->rowCount() > 0) {
            echo "<li style='color:green'>Updated <strong>$slug</strong> to <em>$img</em></li>";
        } else {
            echo "<li style='color:orange'>No change for <strong>$slug</strong> (Maybe already updated or post not found)</li>";
        }
    }
    echo "</ul>";
    echo "<p><strong>Done!</strong> Please check the Blogs page now.</p>";
    echo "<p><a href='pages/blogs.php'>Go to Blogs</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
