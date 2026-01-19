<?php
// setup_affiliates.php
require_once __DIR__ . '/includes/db.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Database Setup for Affiliate System...\n";

try {
    // 1. Create affiliates table
    $sql1 = "CREATE TABLE IF NOT EXISTS affiliates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql1);
    echo "[PASS] 'affiliates' table created or already exists.\n";

    // 2. Add affiliate_id to bookings if not exists
    // Check if column exists first
    $stmt = $pdo->prepare("SHOW COLUMNS FROM bookings LIKE 'affiliate_id'");
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "[INFO] 'affiliate_id' column already exists in bookings.\n";
    } else {
        $sql2 = "ALTER TABLE bookings ADD COLUMN affiliate_id INT DEFAULT NULL AFTER status";
        // Optional: Add FK constraint if desired, but might be strict if affiliates are deleted. 
        // Let's add index for performance.
        $sql2 .= ", ADD INDEX (affiliate_id)";

        $pdo->exec($sql2);
        echo "[PASS] 'affiliate_id' column added to bookings.\n";
    }

    // 3. Seed Test Affiliate
    $testCode = 'TEST01';
    $stmt = $pdo->prepare("SELECT id FROM affiliates WHERE code = ?");
    $stmt->execute([$testCode]);
    if ($stmt->fetch()) {
        echo "[INFO] Test affiliate '$testCode' already exists.\n";
    } else {
        $sql3 = "INSERT INTO affiliates (name, code, email, status) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql3)->execute(['Test Partner', $testCode, 'partner@test.com', 'active']);
        echo "[PASS] Test affiliate '$testCode' created.\n";
    }

} catch (PDOException $e) {
    echo "[FAIL] Database Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Setup Completed Successfully.\n";
?>