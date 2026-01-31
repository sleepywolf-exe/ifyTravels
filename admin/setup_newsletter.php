<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $db = Database::getInstance();

    // Create subscribers table
    $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    $db->execute($sql);
    echo "Subscribers table created successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>