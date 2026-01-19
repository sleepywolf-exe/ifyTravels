<?php
// update_affiliate_schema.php
require_once 'includes/db.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "Starting Schema Update...\n";

    // 1. Add columns to 'affiliates' table if they don't exist
    $columns = [
        'password_hash' => "VARCHAR(255) DEFAULT NULL",
        'commission_rate' => "DECIMAL(5,2) DEFAULT 10.00",
        'last_login' => "DATETIME DEFAULT NULL"
    ];

    foreach ($columns as $col => $def) {
        try {
            // Check if column exists (SQLite specific check, might need adjustment for MySQL if strict)
            // For cross-db compatibility in this simple script, we'll try to add and ignore error if exists
            // Or better, check schema.

            // Generic ADD COLUMN (Works in MySQL and SQLite usually)
            $sql = "ALTER TABLE affiliates ADD COLUMN $col $def";
            $conn->exec($sql);
            echo "Added column: $col\n";
        } catch (PDOException $e) {
            // Assume error means column exists
            echo "Column $col likely exists or error: " . $e->getMessage() . "\n";
        }
    }

    // 2. Create 'referral_clicks' table
    echo "Creating referral_clicks table...\n";
    $clickTableSql = "
    CREATE TABLE IF NOT EXISTS referral_clicks (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        affiliate_id INTEGER NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        referrer_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    // MySQL compatibility adjustment if needed, but 'INTEGER PRIMARY KEY AUTOINCREMENT' is SQLite.
    // Let's use a more generic SQL or check driver.
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        $clickTableSql = "
        CREATE TABLE IF NOT EXISTS referral_clicks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            affiliate_id INT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            referrer_url TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (affiliate_id),
            INDEX (ip_address)
        )";
    }

    $conn->exec($clickTableSql);
    echo "Table 'referral_clicks' ensured.\n";

    echo "Schema Update Complete.\n";

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>