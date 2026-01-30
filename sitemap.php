<?php
/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml for SEO
 */

require_once __DIR__ . '/includes/functions.php';

// Set Content-Type
if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

// Initialize Database using new singleton pattern
$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Static Pages -->
    <url>
        <loc><?php echo base_url(); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo base_url('destinations'); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo base_url('packages'); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo base_url('contact'); ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo base_url('booking'); ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo base_url('partner-program'); ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?php echo base_url('login'); ?></loc>
        <changefreq>yearly</changefreq>
        <priority>0.4</priority>
    </url>

    <!-- Legal Pages -->
    <url>
        <loc><?php echo base_url('pages/legal/privacy.html'); ?></loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc><?php echo base_url('pages/legal/refund.html'); ?></loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc><?php echo base_url('pages/legal/terms.html'); ?></loc>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>

    <!-- Dynamic Destinations -->
    <?php
    try {
        $destinations = $db->fetchAll("SELECT * FROM destinations ORDER BY created_at DESC");
        foreach ($destinations as $dest) {
            // Use shared helper for accurate URL
            $url = destination_url($dest['slug']);
            
            // Calculate last modified
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
            echo "        <priority>0.7</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        echo "<!-- Error loading destinations: " . $e->getMessage() . " -->\n";
    }
    ?>

    <!-- Dynamic Packages -->
    <?php
    try {
        $packages = $db->fetchAll("SELECT * FROM packages ORDER BY created_at DESC");
        foreach ($packages as $pkg) {
            // Use shared helper for accurate URL
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
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n";
        }
    } catch (Exception $e) {
        echo "<!-- Error loading packages: " . $e->getMessage() . " -->\n";
    }
    ?>

</urlset>