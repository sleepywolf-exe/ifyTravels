<?php
// admin/indexing_action.php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/classes/GoogleIndexer.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    exit;
}

// CSRF can be added here if needed, but for now we rely on auth_check session

$action = $_POST['action'] ?? '';

// Check if Service Account Key Exists
if (!file_exists(__DIR__ . '/../includes/config/service_account.json')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Service Account Key Missing! Please upload "service_account.json" to "includes/config/" folder.'
    ]);
    exit;
}

$indexer = new GoogleIndexer();

try {
    $results = [];

    if ($action === 'index_home') {
        // Index Homepage
        $url = base_url();
        $res = $indexer->indexUrl($url, 'URL_UPDATED');
        $results[] = ["url" => $url, "result" => $res];

    } elseif ($action === 'index_all') {
        // 1. Static Pages
        $urls = [
            base_url(),
            base_url('destinations'),
            base_url('packages'),
            base_url('blogs'),
            base_url('contact'),
            base_url('about'),
            base_url('partner-program')
        ];

        // 2. Fetch Packages
        try {
            $packages = $db->fetchAll("SELECT slug FROM packages");
            foreach ($packages as $pkg) {
                if (!empty($pkg['slug'])) {
                    $urls[] = base_url('packages/' . $pkg['slug']);
                }
            }
        } catch (Exception $e) { /* Ignore if table missing */
        }

        // 3. Fetch Destinations
        try {
            $destinations = $db->fetchAll("SELECT slug FROM destinations");
            foreach ($destinations as $dest) {
                if (!empty($dest['slug'])) {
                    $urls[] = base_url('destinations/' . $dest['slug']);
                }
            }
        } catch (Exception $e) { /* Ignore */
        }

        // 4. Fetch Blogs
        try {
            $blogs = $db->fetchAll("SELECT slug FROM blogs");
            foreach ($blogs as $blog) {
                if (!empty($blog['slug'])) {
                    $urls[] = base_url('blogs/' . $blog['slug']);
                }
            }
        } catch (Exception $e) { /* Ignore */
        }

        // 5. Submit All URLs
        foreach ($urls as $url) {
            $res = $indexer->indexUrl($url, 'URL_UPDATED');
            $results[] = ["url" => $url, "result" => $res];

            // Respect API Rate Limits (Default: 600 requests/minute = 10/sec)
            // We sleep 0.1s to be safe
            usleep(100000);
        }
    } else {
        throw new Exception("Unknown Action");
    }

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
