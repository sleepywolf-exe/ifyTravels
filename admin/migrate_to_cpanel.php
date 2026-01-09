<?php
/**
 * migrate_to_cpanel.php
 * 
 * This script migrates your local SQLite database (db/database.db) to a MySQL/MariaDB database (e.g., on cPanel).
 * 
 * INSTRUCTIONS:
 * 1. Open this file and ensure the MySQL Credentials below are correct.
 * 2. Upload the entire project to your cPanel server.
 * 3. Visit example.com/admin/migrate_to_cpanel.php in your browser.
 * 4. Once migration is successful, delete this file or rename it for security.
 */

// --- CONFIGURATION ---
$mysql_host = 'localhost';          // Usually 'localhost' on cPanel if script runs there
$mysql_user = 'bikesraj_ifytravels';
$mysql_pass = 'Secure@123.';
$mysql_db = 'bikesraj_ifytravels';
// ---------------------

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Migration: SQLite -> MySQL</h1>";

// 1. Connect to Source (SQLite)
$sqlitePath = __DIR__ . '/../db/database.db';
if (!file_exists($sqlitePath)) {
    die("Error: SQLite database file not found at $sqlitePath");
}

try {
    $sqlite = new PDO("sqlite:$sqlitePath");
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "<p style='color:green'>✔ Connected to Source (SQLite)</p>";
} catch (PDOException $e) {
    die("Error connecting to SQLite: " . $e->getMessage());
}

// 2. Connect to Target (MySQL)
try {
    $mysql = new PDO("mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8mb4", $mysql_user, $mysql_pass);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✔ Connected to Target (MySQL)</p>";
} catch (PDOException $e) {
    die("Error connecting to MySQL: " . $e->getMessage() . "<br>Check your credentials.");
}

// Helper to migrate a table
function migrateTable($tableName, $createSqlMySQL, $sqlite, $mysql)
{
    echo "<h3>Migrating table: $tableName...</h3>";

    // Drop existing
    $mysql->exec("DROP TABLE IF EXISTS `$tableName`");

    // Create Table
    try {
        $mysql->exec($createSqlMySQL);
        echo " - Table created.<br>";
    } catch (Exception $e) {
        die(" - Error creating table: " . $e->getMessage());
    }

    // Fetch Data
    $rows = $sqlite->query("SELECT * FROM `$tableName`")->fetchAll();
    $count = count($rows);
    echo " - Found $count rows to insert.<br>";

    if ($count > 0) {
        // Prepare Insert Statement
        $columns = array_keys($rows[0]);
        $colNames = implode(", ", array_map(fn($c) => "`$c`", $columns));
        $placeholders = implode(", ", array_fill(0, count($columns), "?"));

        $sql = "INSERT INTO `$tableName` ($colNames) VALUES ($placeholders)";
        $stmt = $mysql->prepare($sql);

        $success = 0;
        foreach ($rows as $row) {
            try {
                $stmt->execute(array_values($row));
                $success++;
            } catch (Exception $e) {
                echo " - Failed to insert row ID " . ($row['id'] ?? '?') . ": " . $e->getMessage() . "<br>";
            }
        }
        echo " - Successfully inserted $success rows.<br>";
    } else {
        echo " - No data to insert.<br>";
    }
    echo "<hr>";
}

// --- DEFINE MYSQL SCHEMAS ---
// Mapped from SQLite schema

$schemas = [
    'users' => "CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(150) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `role` varchar(10) DEFAULT 'user',
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'site_settings' => "CREATE TABLE `site_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_key` varchar(50) NOT NULL,
        `setting_value` text DEFAULT NULL,
        `description` varchar(255) DEFAULT NULL,
        `updated_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'destinations' => "CREATE TABLE `destinations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `slug` varchar(100) NOT NULL,
        `country` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `image_url` varchar(255) DEFAULT NULL,
        `rating` decimal(2,1) DEFAULT 4.5,
        `type` varchar(50) DEFAULT 'International',
        `best_time_to_visit` varchar(100) DEFAULT NULL,
        `is_featured` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'packages' => "CREATE TABLE `packages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `destination_id` int(11) DEFAULT NULL,
        `title` varchar(150) NOT NULL,
        `slug` varchar(150) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `duration` varchar(50) NOT NULL,
        `image_url` varchar(255) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `inclusions` text DEFAULT NULL,
        `exclusions` text DEFAULT NULL,
        `features` text DEFAULT NULL,
        `is_popular` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        KEY `destination_id` (`destination_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'bookings' => "CREATE TABLE `bookings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `package_id` int(11) DEFAULT NULL,
        `package_name` varchar(150) DEFAULT NULL,
        `customer_name` varchar(100) NOT NULL,
        `email` varchar(150) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `travel_date` date DEFAULT NULL,
        `special_requests` text DEFAULT NULL,
        `total_price` decimal(10,2) DEFAULT NULL,
        `status` varchar(20) DEFAULT 'Pending',
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'inquiries' => "CREATE TABLE `inquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(150) NOT NULL,
        `subject` varchar(100) DEFAULT NULL,
        `message` text DEFAULT NULL,
        `status` varchar(20) DEFAULT 'New',
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'testimonials' => "CREATE TABLE `testimonials` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `location` text DEFAULT NULL,
        `rating` int(11) DEFAULT 5,
        `message` text NOT NULL,
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    'password_resets' => "CREATE TABLE `password_resets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` text NOT NULL,
        `token` text NOT NULL,
        `expiry` int(11) NOT NULL,
        `created_at` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

// Execute Migration
foreach ($schemas as $table => $sql) {
    migrateTable($table, $sql, $sqlite, $mysql);
}

echo "<h2>✅ All Done! Migration Complete.</h2>";
echo "<p>Next Step: Check your <code>includes/config.php</code> to point to this new database.</p>";
?>