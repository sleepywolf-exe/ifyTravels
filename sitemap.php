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
    'about' => '0.7',
    'destinations' => '0.9',
    'packages' => '0.9',
    'blogs' => '0.8',
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
    echo "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "        <changefreq>monthly</changefreq>\n";
    echo "        <priority>{$priority}</priority>\n";
    echo "    </url>";
}

// 2. Dynamic Content from Database
// We implement a local DB connection to avoid the 'die()' in includes/db.php if it fails.
$pdo = null;
try {
    if (file_exists(__DIR__ . '/includes/config.php')) {
        require_once __DIR__ . '/includes/config.php';
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } else {
        echo "<!-- Config file not found -->";
    }
} catch (Exception $e) {
    echo "<!-- Database Connection Error: " . $e->getMessage() . " -->";
    // Optional: Output a reachable error URL for visibility
    echo "\n    <url><loc>" . base_url('error/db-connection-failed') . "</loc><priority>0.0</priority></url>\n";
}

if ($pdo) {
    // Helper function for lastmod
    function get_lastmod($row)
    {
        if (!empty($row['updated_at']) && $row['updated_at'] != '0000-00-00 00:00:00') {
            return date('Y-m-d', strtotime($row['updated_at']));
        }
        if (!empty($row['created_at']) && $row['created_at'] != '0000-00-00 00:00:00') {
            return date('Y-m-d', strtotime($row['created_at']));
        }
        return date('Y-m-d');
    }

    // Destinations
    try {
        $sql = "SELECT * FROM destinations ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        $destinations = $stmt->fetchAll();

        if (empty($destinations)) {
            echo "<!-- No destinations found in database -->";
        }

        foreach ($destinations as $dest) {
            $lastMod = get_lastmod($dest);

            echo "\n    <url>\n";
            echo "        <loc>" . destination_url($dest['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>";
        }
    } catch (Exception $e) {
        echo "<!-- Destinations Query Error: " . $e->getMessage() . " -->";
    }

    // Packages
    try {
        $sql = "SELECT * FROM packages ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        $packages = $stmt->fetchAll();

        if (empty($packages)) {
            echo "<!-- No packages found in database -->";
        }

        foreach ($packages as $pkg) {
            $lastMod = get_lastmod($pkg);

            echo "\n    <url>\n";
            echo "        <loc>" . package_url($pkg['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>";
        }
    } catch (Exception $e) {
        echo "<!-- Packages Query Error: " . $e->getMessage() . " -->";
    }
    // Blogs
    try {
        $sql = "SELECT * FROM posts ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        $posts = $stmt->fetchAll();

        if (empty($posts)) {
            echo "<!-- No blog posts found in database -->";
        }

        foreach ($posts as $post) {
            $lastMod = get_lastmod($post);

            echo "\n    <url>\n";
            echo "        <loc>" . base_url('blogs/' . $post['slug']) . "</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.7</priority>\n";
            echo "    </url>";
        }
    } catch (Exception $e) {
        echo "<!-- Blogs Query Error: " . $e->getMessage() . " -->";
    }
}

echo "\n</urlset>";
?>