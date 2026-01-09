<?php
// update_schema.php
require 'includes/config.php';

// Quick DB Class Mock if not loadable easily, but better to load existing
// Trying to load existing
if (file_exists('includes/functions.php')) {
    require 'includes/functions.php';
} else {
    die("Functions file not found.");
}

$db = Database::getInstance();

try {
    // Check if column exists
    $check = $db->fetch("SHOW COLUMNS FROM destinations LIKE 'map_embed'");
    if ($check) {
        echo "Column 'map_embed' already exists.\n";
    } else {
        $db->execute("ALTER TABLE destinations ADD COLUMN map_embed TEXT");
        echo "Column 'map_embed' added successfully.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>