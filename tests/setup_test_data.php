<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $db = Database::getInstance();

    // Check for existing affiliate
    $aff = $db->fetch("SELECT * FROM affiliates WHERE email = 'test@partner.com'");

    if ($aff) {
        $code = $aff['code'];
        echo "Found existing affiliate: $code\n";
    } else {
        $code = 'TEST' . rand(1000, 9999);
        $db->execute("INSERT INTO affiliates (name, email, phone, password, code, status, commission_rate) VALUES (?, ?, ?, ?, ?, 'active', 10.00)", [
            'Test Partner',
            'test@partner.com',
            '1234567890',
            password_hash('password123', PASSWORD_DEFAULT),
            $code
        ]);
        echo "Created new affiliate: $code\n";
    }

    // Ensure we have a package
    $pkg = $db->fetch("SELECT id FROM packages LIMIT 1");
    if (!$pkg) {
        $db->execute("INSERT INTO packages (title, slug, description, price, duration_days, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            'Test Package',
            'test-package',
            'Description',
            10000.00,
            5,
            'assets/images/packages/test.jpg',
            'active'
        ]);
        echo "Created test package.\n";
    } else {
        echo "Package exists.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
