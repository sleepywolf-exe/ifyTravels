<?php
require_once 'includes/db.php';
require_once 'includes/classes/GoogleIndexer.php';

// Set header to plain text for easier reading
header('Content-Type: text/plain');

$indexer = new GoogleIndexer();
$db = Database::getInstance()->getConnection();
$baseUrl = 'https://ifytravels.com/';

echo "🚀 Starting Full Site Indexing...\n";
echo "================================\n\n";

$urlsToIndex = [
    $baseUrl,
    $baseUrl . 'about',
    $baseUrl . 'contact',
    $baseUrl . 'packages',
    $baseUrl . 'destinations',
    $baseUrl . 'blogs'
];

// Fetch all dynamic URLs
echo "Fetching dynamic URLs from database...\n";

// Packages
$stmt = $db->query("SELECT slug FROM packages WHERE status = 'Active'");
$pkgCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $urlsToIndex[] = $baseUrl . 'package/' . $row['slug'];
    $pkgCount++;
}
echo "Found $pkgCount packages.\n";

// Destinations
$stmt = $db->query("SELECT slug FROM destinations WHERE status = 'Active'");
$destCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $urlsToIndex[] = $baseUrl . 'destination/' . $row['slug'];
    $destCount++;
}
echo "Found $destCount destinations.\n";

// Blogs
$stmt = $db->query("SELECT slug FROM blogs WHERE status = 'Published'");
$blogCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $urlsToIndex[] = $baseUrl . 'blog/' . $row['slug'];
    $blogCount++;
}
echo "Found $blogCount blogs.\n";

$total = count($urlsToIndex);
echo "\nTotal URLs to Index: $total\n";
echo "--------------------------------\n";

// Batch process (Google API has quotas, but loop is fine for <100 typically)
foreach ($urlsToIndex as $index => $url) {
    echo "[" . ($index + 1) . "/$total] Submitting: $url ... ";

    try {
        $result = $indexer->indexUrl($url, 'URL_UPDATED');

        if (isset($result['status']) && $result['status'] === 'success') {
            echo "✅ SUCCESS\n";
        } else {
            echo "⚠️  Response: " . json_encode($result) . "\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }

    // Small pause to be nice to the API
    usleep(200000); // 0.2 seconds
}

echo "\n================================\n";
echo "🎉 Indexing Requests Completed!\n";
?>