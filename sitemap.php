<?php
/**
 * Sitemap Index
 * Points to sub-sitemaps for better organization.
 */

require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() !== 'cli') {
    header("Content-Type: application/xml; charset=utf-8");
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc><?php echo base_url('sitemap-main.xml'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>
    <sitemap>
        <loc><?php echo base_url('sitemap-destinations.xml'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>
    <sitemap>
        <loc><?php echo base_url('sitemap-packages.xml'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>
</sitemapindex>