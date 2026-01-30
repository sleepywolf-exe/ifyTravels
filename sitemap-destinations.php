<?php
/**
 * Destinations Sitemap
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
        $destinations = $db->fetchAll("SELECT * FROM destinations ORDER BY created_at DESC");
        foreach ($destinations as $dest) {
            $url = destination_url($dest['slug']);
            
            if (!empty($dest['updated_at'])) {
                $lastMod = date('Y-m-d', strtotime($dest['updated_at']));
            } elseif (!empty($dest['created_at'])) {
                $lastMod = date('Y-m-d', strtotime($dest['created_at']));
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
