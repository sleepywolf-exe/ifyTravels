<?php
// setup_affiliates.php
require_once __DIR__ . '/includes/db.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting Database Setup for Affiliate System...\n";

try {
    // Detect Driver
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    // 1. Create affiliates table
    $enumVal = ($driver === 'sqlite') ? "TEXT CHECK(status IN ('active', 'inactive'))" : "ENUM('active', 'inactive')";
    $default = "DEFAULT 'active'";
    $autoInc = ($driver === 'sqlite') ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $charset = ($driver === 'mysql') ? "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4" : "";

    $sql1 = "CREATE TABLE IF NOT EXISTS affiliates (
        id $autoInc,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL,
        status $enumVal $default,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;";

    $pdo->exec($sql1);
    echo "[PASS] 'affiliates' table created or already exists.\n";

    // 2. Add affiliate_id to bookings if not exists
    $exists = false;
    if ($driver === 'sqlite') {
        $res = $pdo->query("PRAGMA table_info(bookings)");
        foreach ($res as $row) {
            if ($row['name'] === 'affiliate_id') {
                $exists = true;
                break;
            }
        }
    } else {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM bookings LIKE 'affiliate_id'");
        $stmt->execute();
        if ($stmt->fetch())
            $exists = true;
    }

    if ($exists) {
        echo "[INFO] 'affiliate_id' column already exists in bookings.\n";
    } else {
        $sql2 = "ALTER TABLE bookings ADD COLUMN affiliate_id INT DEFAULT NULL";
        if ($driver === 'mysql') {
            $sql2 .= " AFTER status, ADD INDEX (affiliate_id)";
        }

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