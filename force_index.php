<?php
// Enable Error Reporting for Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db.php';
require_once 'includes/classes/GoogleIndexer.php';

// Set header to plain text for easier reading
header('Content-Type: text/plain');

try {
    $indexer = new GoogleIndexer();
    // Get connection directly to ensure we catch connection errors here
    $db = Database::getInstance();
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

    // helper to run query and fetch
    function getSlugs($db, $table, $type, $baseUrl)
    {
        $urls = [];
        $sql = "SELECT slug FROM $table WHERE status = " . ($table === 'blogs' ? "'Published'" : "'Active'");
        $stmt = $db->query($sql);

        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $urls[] = $baseUrl . $type . '/' . $row['slug'];
            }
        } else {
            echo "⚠️  Warning: Could not fetch $table.\n";
        }
        return $urls;
    }

    // Packages
    $pkgUrls = getSlugs($db, 'packages', 'package', $baseUrl);
    echo "Found " . count($pkgUrls) . " packages.\n";
    $urlsToIndex = array_merge($urlsToIndex, $pkgUrls);

    // Destinations
    $destUrls = getSlugs($db, 'destinations', 'destination', $baseUrl);
    echo "Found " . count($destUrls) . " destinations.\n";
    $urlsToIndex = array_merge($urlsToIndex, $destUrls);

    // Blogs
    $blogUrls = getSlugs($db, 'blogs', 'blog', $baseUrl);
    echo "Found " . count($blogUrls) . " blogs.\n";
    $urlsToIndex = array_merge($urlsToIndex, $blogUrls);

    $total = count($urlsToIndex);
    echo "\nTotal URLs to Index: $total\n";
    echo "--------------------------------\n";

    // Batch process
    foreach ($urlsToIndex as $index => $url) {
        echo "[" . ($index + 1) . "/$total] Submitting: $url ... ";

        try {
            $result = $indexer->indexUrl($url, 'URL_UPDATED');

            // Handle array response from indexUrl
            if (isset($result['status']) && $result['status'] === 'success') {
                echo "✅ SUCCESS\n";
            } else {
                echo "⚠️  Response: " . json_encode($result) . "\n";
            }
        } catch (Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }

        // Small pause to avoid hitting rate limits too hard
        usleep(200000);
    }

    echo "\n================================\n";
    echo "🎉 Indexing Requests Completed!\n";

} catch (Exception $e) {
    echo "\n\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}