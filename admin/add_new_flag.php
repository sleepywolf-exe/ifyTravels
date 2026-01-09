<?php
// Fix includes using __DIR__
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Get DB instance
    $db = Database::getInstance();

    // Add is_new to packages if not exists
    $columns = $db->fetchAll("SHOW COLUMNS FROM packages LIKE 'is_new'");
    if (empty($columns)) {
        $db->query("ALTER TABLE packages ADD COLUMN is_new TINYINT(1) DEFAULT 0 AFTER is_popular");
        echo "Added is_new to packages.\n";
    } else {
        echo "is_new already exists in packages.\n";
    }

    // Add is_new to destinations if not exists
    $columnsDest = $db->fetchAll("SHOW COLUMNS FROM destinations LIKE 'is_new'");
    if (empty($columnsDest)) {
        $db->query("ALTER TABLE destinations ADD COLUMN is_new TINYINT(1) DEFAULT 0 AFTER is_featured");
        echo "Added is_new to destinations.\n";
    } else {
        echo "is_new already exists in destinations.\n";
    }

    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>