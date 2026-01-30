<?php
/**
 * Packages Sitemap
 */

require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
    try {
        $packages = $db->fetchAll("SELECT * FROM packages ORDER BY created_at DESC");
        foreach ($packages as $pkg) {
            $url = package_url($pkg['slug']);

            if (!empty($pkg['updated_at'])) {
                $lastMod = date('Y-m-d', strtotime($pkg['updated_at']));
            } elseif (!empty($pkg['created_at'])) {
                $lastMod = date('Y-m-d', strtotime($pkg['created_at']));
            } else {
                $lastMod = date('Y-m-d');
            }

            echo "    <url>\n";
            echo "        <loc>{$url}</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.8</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        echo "<!-- Error: " . $e->getMessage() . " -->";
    }
    ?>
</urlset>
