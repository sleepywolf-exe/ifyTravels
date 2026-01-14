<?php
/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml for SEO
 */

require_once __DIR__ . '/includes/db.php';

// Set Content-Type
if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

// Determine Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $domain;

// Initialize Database
$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Static Pages -->
    <url>
        <loc>
            <?php echo $baseUrl; ?>/
        </loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/destinations
        </loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/packages
        </loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/contact
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/booking
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Legal Pages -->
    <url>
        <loc>
            <?php echo $baseUrl; ?>/pages/legal/privacy.html
        </loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/pages/legal/refund.html
        </loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>
            <?php echo $baseUrl; ?>/pages/legal/terms.html
        </loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>

    <!-- Dynamic Destinations -->
    <?php
    try {
        $destinations = $db->fetchAll("SELECT slug, updated_at FROM destinations ORDER BY created_at DESC");
        foreach ($destinations as $dest) {
            $slug = htmlspecialchars($dest['slug']);
            // Use updated_at if available, else current time or specific date
            $lastMod = !empty($dest['updated_at']) ? date('Y-m-d', strtotime($dest['updated_at'])) : date('Y-m-d');
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/destinations/{$slug}</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.7</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        // Log error silently
        error_log("Sitemap Error (Destinations): " . $e->getMessage());
    }
    ?>

    <!-- Dynamic Packages -->
    <?php
    try {
        $packages = $db->fetchAll("SELECT slug, updated_at FROM packages ORDER BY created_at DESC");
        foreach ($packages as $pkg) {
            $slug = htmlspecialchars($pkg['slug']);
            $lastMod = !empty($pkg['updated_at']) ? date('Y-m-d', strtotime($pkg['updated_at'])) : date('Y-m-d');
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/packages/{$slug}</loc>\n";
            echo "        <lastmod>{$lastMod}</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        error_log("Sitemap Error (Packages): " . $e->getMessage());
    }
    ?>

</urlset>