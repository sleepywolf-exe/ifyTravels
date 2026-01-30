<?php
/**
 * Main Sitemap (Static Pages)
 */

require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo base_url(); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo base_url('destinations'); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo base_url('packages'); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo base_url('contact'); ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo base_url('booking'); ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
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
    
    <!-- Legal -->
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
</urlset>
