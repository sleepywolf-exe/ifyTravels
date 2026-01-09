<?php
require_once '../includes/config.php';

try {
    // Add is_new to packages if not exists
    $columns = $db->fetchAll("SHOW COLUMNS FROM packages LIKE 'is_new'");
    if (empty($columns)) {
        $db->query("ALTER TABLE packages ADD COLUMN is_new TINYINT(1) DEFAULT 0 AFTER is_popular");
        echo "Added is_new to packages.<br>";
    }

    // Add is_new to destinations if not exists
    $columnsDest = $db->fetchAll("SHOW COLUMNS FROM destinations LIKE 'is_new'");
    if (empty($columnsDest)) {
        $db->query("ALTER TABLE destinations ADD COLUMN is_new TINYINT(1) DEFAULT 0 AFTER is_featured");
        echo "Added is_new to destinations.<br>";
    }

    echo "Migration completed successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>