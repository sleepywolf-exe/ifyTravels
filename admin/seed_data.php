<?php
// admin/seed_data.php
require 'auth_check.php';
// require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();

echo "Seeding Data...\n";

// Destinations
$destinations = [
    [
        'name' => 'Bali',
        'slug' => 'bali',
        'image_url' => 'assets/images/destinations/bali.png',
        'description' => 'Experience the magic of the Island of the Gods.',
        'country' => 'Indonesia',
        'type' => 'International',
        'rating' => 4.8
    ],
    [
        'name' => 'Dubai',
        'slug' => 'dubai',
        'image_url' => 'assets/images/destinations/dubai.jpg',
        'description' => 'Luxury, shopping, and ultramodern architecture.',
        'country' => 'UAE',
        'type' => 'International',
        'rating' => 4.9
    ],
    [
        'name' => 'Thailand',
        'slug' => 'thailand',
        'image_url' => 'assets/images/destinations/thailand.jpg',
        'description' => 'A tropical paradise with beautiful beaches.',
        'country' => 'Thailand',
        'type' => 'International',
        'rating' => 4.7
    ],
    [
        'name' => 'Paris',
        'slug' => 'paris',
        'image_url' => 'assets/images/destinations/paris.png',
        'description' => 'The City of Light, known for its cafe culture, Eiffel Tower, and masterpieces of art.',
        'country' => 'France',
        'type' => 'International',
        'rating' => 4.9
    ]
];

foreach ($destinations as $d) {
    // Check if exists
    $exists = $db->fetch("SELECT id FROM destinations WHERE slug = ?", [$d['slug']]);
    if (!$exists) {
        $db->execute(
            "INSERT INTO destinations (name, slug, image_url, description, country, type, rating) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$d['name'], $d['slug'], $d['image_url'], $d['description'], $d['country'], $d['type'], $d['rating']]
        );
        echo "Inserted Destination: " . $d['name'] . "\n";
    }
}

// Packages
// Need IDs
$bali = $db->fetch("SELECT id FROM destinations WHERE slug = 'bali'")['id'];
$dubai = $db->fetch("SELECT id FROM destinations WHERE slug = 'dubai'")['id'];
$thai = $db->fetch("SELECT id FROM destinations WHERE slug = 'thailand'")['id'];
$paris = $db->fetch("SELECT id FROM destinations WHERE slug = 'paris'")['id'];

$packages = [
    [
        'destination_id' => $bali,
        'title' => 'Bali Paradise Escape',
        'slug' => 'bali-paradise',
        'price' => 45000,
        'duration' => '5 Days / 4 Nights',
        'image_url' => 'assets/images/packages/bali.png',
        'features' => json_encode(['Beachfront Villa', 'Daily Breakfast', 'Island Tour']),
        'is_popular' => 1
    ],
    [
        'destination_id' => $dubai,
        'title' => 'Dubai Luxury Stay',
        'slug' => 'dubai-luxury',
        'price' => 65000,
        'duration' => '4 Days / 3 Nights',
        'image_url' => 'assets/images/packages/dubai.png',
        'features' => json_encode(['Burj Khalifa', 'Desert Safari', '5 Star Hotel']),
        'is_popular' => 1
    ],
    [
        'destination_id' => $thai,
        'title' => 'Phuket & Krabi Adventure',
        'slug' => 'phuket-krabi',
        'price' => 35000,
        'duration' => '6 Days / 5 Nights',
        'image_url' => 'assets/images/packages/thailand.jpg',
        'features' => json_encode(['Island Hopping', 'Snorkeling', 'City Tour']),
        'is_popular' => 1
    ],
    [
        'destination_id' => $paris,
        'title' => 'Paris Romance Package',
        'slug' => 'paris-romance',
        'price' => 85000,
        'duration' => '5 Days / 4 Nights',
        'image_url' => 'assets/images/destinations/paris.png', // Fallback to destination image if package image unavailable
        'features' => json_encode(['Eiffel Tower Dinner', 'Seine Cruise', 'Louvre Museum']),
        'is_popular' => 1
    ],
    [
        'destination_id' => $paris,
        'title' => 'Paris & Swiss Delight',
        'slug' => 'paris-swiss',
        'price' => 120000,
        'duration' => '8 Days / 7 Nights',
        'image_url' => 'assets/images/destinations/paris.png',
        'features' => json_encode(['City Tour', 'Swiss Alps', 'Train Transfer']),
        'is_popular' => 0
    ]
];

foreach ($packages as $p) {
    $exists = $db->fetch("SELECT id FROM packages WHERE slug = ?", [$p['slug']]);
    if (!$exists) {
        $db->execute(
            "INSERT INTO packages (destination_id, title, slug, price, duration, image_url, features, is_popular) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$p['destination_id'], $p['title'], $p['slug'], $p['price'], $p['duration'], $p['image_url'], $p['features'], $p['is_popular']]
        );
        echo "Inserted Package: " . $p['title'] . "\n";
    }
}

echo "Seeding Complete.\n";
?>