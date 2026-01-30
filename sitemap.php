<?php
/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml on the fly with Database content.
 */

require_once __DIR__ . '/includes/functions.php';

// Set Header
if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 1. Static Pages
$staticPages = [
    '' => '1.0',
    'destinations' => '0.9',
    'packages' => '0.9',
    'contact' => '0.5',
    'booking' => '0.6',
    'partner-program' => '0.6',
    'login' => '0.4',
    'pages/legal/privacy.html' => '0.3',
    'pages/legal/refund.html' => '0.3',
    'pages/legal/terms.html' => '0.3'
];

foreach ($staticPages as $path => $priority) {
    echo "\n    <url>\n";
    echo "        <loc>" . base_url($path) . "</loc>\n";
    echo "        <changefreq>monthly</changefreq>\n";
    echo "        <priority>{$priority}</priority>\n";
    echo "    </url>";
}

// 2. Dynamic Content from Database
try {
    $db = Database::getInstance();
    
    // Destinations
    $destinations = $db->fetchAll("SELECT slug, updated_at, created_at FROM destinations ORDER BY created_at DESC");
    if ($destinations) {
        foreach ($destinations as $dest) {
            $lastMod = !empty($dest['updated_at']) ? date('Y-m-d', strtotime($dest['updated_at'])) : date('Y-m-d', strtotime($dest['created_at']));
            echo "\n    <url>\n";
            echo "        <loc>" . destination_url($dest['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>";
        }
    }

    // Packages
    $packages = $db->fetchAll("SELECT slug, updated_at, created_at FROM packages ORDER BY created_at DESC");
    if ($packages) {
        foreach ($packages as $pkg) {
            $lastMod = !empty($pkg['updated_at']) ? date('Y-m-d', strtotime($pkg['updated_at'])) : date('Y-m-d', strtotime($pkg['created_at']));
            echo "\n    <url>\n";
            echo "        <loc>" . package_url($pkg['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>";
        }
    }

} catch (Exception $e) {
    // Log error but output valid XML so search engines don't choke completely
    echo "<!-- Database Error: " . $e->getMessage() . " -->";
}

echo "\n</urlset>";
?>