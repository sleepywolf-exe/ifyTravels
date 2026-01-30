<?php
/**
 * Static Sitemap Generator
 * Run this script to generate physical .xml files in the root directory.
 */

// Ensure we are asking for specific environment if needed, or just mock $_SERVER for base_url
if (php_sapi_name() === 'cli' && !isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'ifytravels.com';
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_URI'] = '/';
}

require_once __DIR__ . '/includes/functions.php';

// Check Admin Auth (Optional, but good practice if exposed via Web)
// if (!is_admin()) { die("Access Denied"); }

$db = Database::getInstance();
$generatedFiles = [];

// 1. Generate sitemap-pages.xml (Static Pages)
$pagesXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$pagesXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$staticPages = [
    '' => '1.0', // Home
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
    // Only use base_url() logic but force https://ifytravels.com if local/cli
    $url = base_url($path);
    // Ensure it looks like a production URL
    if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
       $url = str_replace(['http://localhost', 'http://127.0.0.1'], 'https://ifytravels.com', $url);
       // Remove any potential project/ folder path if testing locally
       // For now, assume base_url matches production or we patch it.
    }
    
    // Hard override for CLI reliability in this specific task
    $url = 'https://ifytravels.com/' . ltrim($path, '/');

    $pagesXml .= "    <url>\n";
    $pagesXml .= "        <loc>{$url}</loc>\n";
    $pagesXml .= "        <changefreq>monthly</changefreq>\n";
    $pagesXml .= "        <priority>{$priority}</priority>\n";
    $pagesXml .= "    </url>\n";
}
$pagesXml .= '</urlset>';
file_put_contents(__DIR__ . '/sitemap-pages.xml', $pagesXml);
$generatedFiles[] = 'sitemap-pages.xml';

// 2. Generate sitemap-destinations.xml
$destXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$destXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
try {
    $destinations = $db->fetchAll("SELECT * FROM destinations ORDER BY created_at DESC");
    foreach ($destinations as $dest) {
        $url = 'https://ifytravels.com/destinations/' . $dest['slug'];
        $lastMod = !empty($dest['updated_at']) ? date('Y-m-d', strtotime($dest['updated_at'])) : date('Y-m-d');
        
        $destXml .= "    <url>\n";
        $destXml .= "        <loc>{$url}</loc>\n";
        $destXml .= "        <lastmod>{$lastMod}</lastmod>\n";
        $destXml .= "        <changefreq>weekly</changefreq>\n";
        $destXml .= "        <priority>0.8</priority>\n";
        $destXml .= "    </url>\n";
    }
} catch (Exception $e) { /* Ignore */ }
$destXml .= '</urlset>';
file_put_contents(__DIR__ . '/sitemap-destinations.xml', $destXml);
$generatedFiles[] = 'sitemap-destinations.xml';

// 3. Generate sitemap-packages.xml
$pkgXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$pkgXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
try {
    $packages = $db->fetchAll("SELECT * FROM packages ORDER BY created_at DESC");
    foreach ($packages as $pkg) {
        $url = 'https://ifytravels.com/packages/' . $pkg['slug'];
        $lastMod = !empty($pkg['updated_at']) ? date('Y-m-d', strtotime($pkg['updated_at'])) : date('Y-m-d');
        
        $pkgXml .= "    <url>\n";
        $pkgXml .= "        <loc>{$url}</loc>\n";
        $pkgXml .= "        <lastmod>{$lastMod}</lastmod>\n";
        $pkgXml .= "        <changefreq>weekly</changefreq>\n";
        $pkgXml .= "        <priority>0.8</priority>\n";
        $pkgXml .= "    </url>\n";
    }
} catch (Exception $e) { /* Ignore */ }
$pkgXml .= '</urlset>';
file_put_contents(__DIR__ . '/sitemap-packages.xml', $pkgXml);
$generatedFiles[] = 'sitemap-packages.xml';

// 4. Generate sitemap.xml (Index)
$indexXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$indexXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($generatedFiles as $file) {
    if ($file === 'sitemap.xml') continue;
    $url = 'https://ifytravels.com/' . $file;
    $lastMod = date('Y-m-d');
    $indexXml .= "    <sitemap>\n";
    $indexXml .= "        <loc>{$url}</loc>\n";
    $indexXml .= "        <lastmod>{$lastMod}</lastmod>\n";
    $indexXml .= "    </sitemap>\n";
}
$indexXml .= '</sitemapindex>';
file_put_contents(__DIR__ . '/sitemap.xml', $indexXml);

echo "Successfully generated static XML files:\n";
echo "- sitemap.xml\n";
foreach ($generatedFiles as $f) {
    echo "- $f\n";
}
?>
