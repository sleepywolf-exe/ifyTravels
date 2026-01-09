<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Test</h1>";

// 1. Config
echo "<h2>1. Includes</h2>";
try {
    require __DIR__ . '/../includes/config.php';
    require __DIR__ . '/../includes/functions.php';
    echo "Config & Functions loaded.<br>";
} catch (Exception $e) {
    die("Include Error: " . $e->getMessage());
}

// 2. Database
echo "<h2>2. Database Connection</h2>";
try {
    $db = Database::getInstance();
    echo "DB Instance created.<br>";
    $ver = $db->fetch("SELECT VERSION()");
    print_r($ver);
    echo "<br>Connected successfully.<br>";
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

// 3. Loader
echo "<h2>3. Data Loader</h2>";
try {
    require __DIR__ . '/../data/loader.php';
    echo "Loader loaded.<br>";

    $packages = getPackages();
    echo "Fetched " . count($packages) . " packages.<br>";

    if (count($packages) > 0) {
        $p = $packages[0];
        echo "First Package: " . htmlspecialchars($p['title']) . "<br>";
        echo "Features Type: " . gettype($p['features']) . "<br>";
        print_r($p['features']);
    }
} catch (Exception $e) {
    echo "Loader Error: " . $e->getMessage();
}
?>