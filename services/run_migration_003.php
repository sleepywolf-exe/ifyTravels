<?php
require_once __DIR__ . '/../includes/functions.php';

try {
    $db = Database::getInstance();
    $sql = file_get_contents(__DIR__ . '/../db/migrations/003_add_password_reset_to_affiliates.sql');

    // Split by semicolon if multiple queries, but here it is just one ALTER
    $db->execute($sql);

    echo "Migration 003 applied successfully.\n";
} catch (Exception $e) {
    echo "Error applying migration: " . $e->getMessage() . "\n";
}
?>