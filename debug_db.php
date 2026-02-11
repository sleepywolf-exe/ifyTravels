<?php
// debug_db.php
// Script to test database connection variants

require_once 'includes/config.php';

echo "Testing Database Connection...\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "------------------------\n";

// Test 1: As Configured
echo "Test 1 (Configured): ";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "[OK]\n";
} catch (Exception $e) {
    echo "[FAIL] - " . $e->getMessage() . "\n";
}

// Test 2: 127.0.0.1
echo "Test 2 (127.0.0.1): ";
try {
    $dsn = "mysql:host=127.0.0.1;dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "[OK]\n";
} catch (Exception $e) {
    echo "[FAIL] - " . $e->getMessage() . "\n";
}

// Test 3: localhost
echo "Test 3 (localhost): ";
try {
    $dsn = "mysql:host=localhost;dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "[OK]\n";
} catch (Exception $e) {
    echo "[FAIL] - " . $e->getMessage() . "\n";
}

// Test 4: Socket (Common locations)
$sockets = [
    '/Applications/MAMP/tmp/mysql/mysql.sock',
    '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
    '/tmp/mysql.sock',
    '/var/mysql/mysql.sock'
];

foreach ($sockets as $socket) {
    echo "Test Socket ($socket): ";
    if (file_exists($socket)) {
        try {
            $dsn = "mysql:unix_socket=$socket;dbname=" . DB_NAME;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            echo "[OK]\n";
        } catch (Exception $e) {
            echo "[FAIL] - " . $e->getMessage() . "\n";
        }
    } else {
        echo "[SKIP] (File not found)\n";
    }
}
