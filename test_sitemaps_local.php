<?php
// Test Script to verify Sitemap URLs
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Starting test...\n";
$files = [
    'sitemap.php',
    'sitemap-main.php',
    'sitemap-destinations.php',
    'sitemap-packages.php'
];

foreach ($files as $file) {
    echo "Testing $file... ";
    ob_start();
    include $file;
    $output = ob_get_clean();
    
    if (strpos($output, '<?xml') !== false && strpos($output, '<urlset') !== false || strpos($output, '<sitemapindex') !== false) {
        echo "OK (Valid XML generated)\n";
    } else {
        echo "FAIL (Invalid output)\n";
        echo substr($output, 0, 100) . "...\n";
    }
}
?>
