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
        // Index Homepage + Key Pages
        $urls = [
            base_url(),
            base_url('destinations'),
            base_url('packages'),
            base_url('blogs'),
            base_url('contact')
        ];

        foreach ($urls as $url) {
            $res = $indexer->indexUrl($url, 'URL_UPDATED');
            $results[] = ["url" => $url, "result" => $res];
            // Small delay to be nice to API
            usleep(200000); // 0.2s
        }
    } else {
        throw new Exception("Unknown Action");
    }

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
