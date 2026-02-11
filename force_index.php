<?php
// force_index.php
// Script to force submission of all URLs to Google Indexing API

header('Content-Type: text/plain');

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/classes/GoogleIndexer.php';

// Disable timeout
set_time_limit(0);

echo "🚀 Starting Bulk Indexing Job...\n";
echo "---------------------------------\n";

$indexer = new GoogleIndexer();
$db = Database::getInstance();
$count = 0;
$errors = 0;

function index_url($indexer, $url)
{
    global $count, $errors;
    echo "Submitting: $url ... ";
    $result = $indexer->indexUrl($url, 'URL_UPDATED');

    if ($result['status'] === 'success') {
        echo "[OK] \n";
        $count++;
    } else {
        echo "[FAIL] - " . ($result['message'] ?? 'Unknown Error') . "\n";
        $errors++;
    }
    // Respect API Quota (approx)
    sleep(1);
}

// 1. Static Pages
$staticPages = [
    '',
    'about',
    'destinations',
    'packages',
    'blogs',
    'contact',
    'booking'
];

foreach ($staticPages as $p) {
    index_url($indexer, base_url($p));
}

// 2. Destinations
$dests = $db->fetchAll("SELECT slug FROM destinations");
foreach ($dests as $d) {
    index_url($indexer, destination_url($d['slug']));
}

// 3. Packages
$pkgs = $db->fetchAll("SELECT slug FROM packages");
foreach ($pkgs as $p) {
    index_url($indexer, package_url($p['slug']));
}

// 4. Blogs
$posts = $db->fetchAll("SELECT slug FROM posts");
foreach ($posts as $p) {
    index_url($indexer, base_url('blogs/' . $p['slug']));
}

echo "---------------------------------\n";
echo "✅ Job Complete.\n";
echo "Total Submitted: $count\n";
echo "Total Failed: $errors\n";
