<?php
// services/search_packages.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$db = Database::getInstance();
$query = $_GET['q'] ?? '';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$searchTerm = "%{$query}%";

// Search by Title, Duration, or Destination Name
$sql = "
    SELECT p.id, p.title, p.slug, p.price, p.duration, p.image_url, p.is_popular, p.features, d.name as destination_name 
    FROM packages p 
    LEFT JOIN destinations d ON p.destination_id = d.id 
    WHERE p.title LIKE ? 
    OR d.name LIKE ?
    OR p.features LIKE ? 
    ORDER BY p.is_popular DESC, p.created_at DESC 
    LIMIT 10
";

$results = $db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);

// Process results for frontend
$formatted = array_map(function ($pkg) {
    return [
        'title' => $pkg['title'],
        'slug' => $pkg['slug'], // Assuming base_url handling on frontend or here
        'price' => number_format($pkg['price']),
        'duration' => $pkg['duration'],
        'image' => base_url($pkg['image_url']),
        'destination' => $pkg['destination_name'],
        'features' => json_decode($pkg['features'] ?? '[]'),
        'is_popular' => (bool) $pkg['is_popular'],
        'url' => package_url($pkg['slug'])
    ];
}, $results);

echo json_encode($formatted);
?>