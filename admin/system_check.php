<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';

echo "<html><head><title>System Diagnostic</title><style>
body { font-family: Arial; padding: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
pre { background: #000; color: #0f0; padding: 10px; overflow: auto; }
</style></head><body>";

echo "<h1>🔍 ifyTravels System Diagnostic</h1>";

$db = Database::getInstance();
$pdo = $db->getConnection();

// Test 1: Database Connection
echo "<div class='section'><h2>1. Database Connection</h2>";
try {
    $pdo->query("SELECT 1");
    echo "<span class='success'>✅ Database connected successfully</span><br>";
    echo "Database: " . DB_NAME . "<br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Database connection failed: " . $e->getMessage() . "</span>";
    exit;
}
echo "</div>";

// Test 2: Check packages table schema
echo "<div class='section'><h2>2. Packages Table Schema</h2>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM packages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $columnNames = array_column($columns, 'Field');
    echo "Total columns: " . count($columnNames) . "<br>";
    echo "<pre>" . implode("\n", $columnNames) . "</pre>";

    if (in_array('trust_badges', $columnNames)) {
        echo "<span class='success'>✅ 'trust_badges' column EXISTS</span><br>";
    } else {
        echo "<span class='error'>❌ 'trust_badges' column MISSING</span><br>";
        echo "Attempting to add it...<br>";
        try {
            $pdo->exec("ALTER TABLE packages ADD COLUMN trust_badges TEXT DEFAULT NULL");
            echo "<span class='success'>✅ Column added successfully!</span><br>";
        } catch (Exception $e) {
            echo "<span class='error'>❌ Failed to add: " . $e->getMessage() . "</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error checking schema: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 3: Check destinations table
echo "<div class='section'><h2>3. Destinations Table Schema</h2>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM destinations");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total columns: " . count($columns) . "<br>";
    $columnNames = array_column($columns, 'Field');
    echo "<pre>" . implode("\n", $columnNames) . "</pre>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 4: Test INSERT capability
echo "<div class='section'><h2>4. Database Write Test</h2>";
try {
    // Try to get one destination for foreign key
    $dest = $pdo->query("SELECT id FROM destinations LIMIT 1")->fetch();
    if (!$dest) {
        echo "<span class='warning'>⚠️  No destinations exist. Creating a test one...</span><br>";
        $pdo->exec("INSERT INTO destinations (name, slug, type, description, rating, image_url) 
                    VALUES ('Test Destination', 'test-dest', 'International', 'Test', '4.5', 'test.jpg')");
        $dest = $pdo->query("SELECT id FROM destinations ORDER BY id DESC LIMIT 1")->fetch();
    }

    $testPackage = [
        'title' => 'System Test Package ' . time(),
        'slug' => 'test-package-' . time(),
        'destination_id' => $dest['id'],
        'price' => 10000,
        'duration' => '3 Days',
        'description' => 'Test package for diagnostics',
        'image_url' => 'test.jpg',
        'is_popular' => 0,
        'features' => '[]',
        'inclusions' => '[]',
        'exclusions' => '[]',
        'activities' => '[]',
        'themes' => '[]',
        'trust_badges' => '["secure_payment"]'
    ];

    $sql = "INSERT INTO packages (title, slug, destination_id, price, duration, description, image_url, is_popular, features, inclusions, exclusions, activities, themes, trust_badges) 
            VALUES (:title, :slug, :destination_id, :price, :duration, :description, :image_url, :is_popular, :features, :inclusions, :exclusions, :activities, :themes, :trust_badges)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($testPackage);

    echo "<span class='success'>✅ Test package created successfully!</span><br>";
    echo "Package ID: " . $pdo->lastInsertId() . "<br>";

    // Clean up
    $pdo->exec("DELETE FROM packages WHERE title LIKE 'System Test Package%'");
    echo "<span class='success'>✅ Test package cleaned up</span><br>";

} catch (Exception $e) {
    echo "<span class='error'>❌ INSERT test failed: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// Test 5: Check file modifications
echo "<div class='section'><h2>5. File Integrity Check</h2>";
$criticalFiles = [
    'admin/packages.php' => 'trust_badges',
    'includes/functions.php' => 'trust_badges',
    'data/loader.php' => 'trust_badges',
    'pages/package-details.php' => 'trust_badges'
];

foreach ($criticalFiles as $file => $searchTerm) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (strpos($content, $searchTerm) !== false) {
            echo "<span class='success'>✅ $file contains '$searchTerm'</span><br>";
        } else {
            echo "<span class='error'>❌ $file MISSING '$searchTerm' - file not updated!</span><br>";
        }
    } else {
        echo "<span class='error'>❌ $file does not exist!</span><br>";
    }
}
echo "</div>";

// Test 6: Check current packages
echo "<div class='section'><h2>6. Current Data Check</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
    echo "Total packages in database: <strong>$count</strong><br>";

    $count = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    echo "Total destinations in database: <strong>$count</strong><br>";

    if ($count > 0) {
        $recent = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC LIMIT 1")->fetch();
        if ($recent) {
            echo "<h3>Most Recent Package:</h3>";
            echo "Title: " . htmlspecialchars($recent['title']) . "<br>";
            echo "Trust Badges: " . htmlspecialchars($recent['trust_badges'] ?? 'NULL') . "<br>";
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>Error: " . $e->getMessage() . "</span>";
}
echo "</div>";

echo "<div class='section' style='background: #e3f2fd;'><h2>✅ Diagnostic Complete</h2>";
echo "<p>If all tests pass above, the system should be working. If you still can't create packages:</p>";
echo "<ol>";
echo "<li>Make sure you uploaded ALL modified files to the server</li>";
echo "<li>Clear your browser cache and try again</li>";
echo "<li>Check if there are any JavaScript errors in the browser console (F12)</li>";
echo "<li>Try creating a package and note the exact error message</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>