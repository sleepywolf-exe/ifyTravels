<?php
// services/update_hero_subtitle.php
require_once __DIR__ . '/../includes/db.php';
$db = Database::getInstance();

$key = 'hero_subtitle';
$value = "Curated luxury travel experiences designed just for you. 🏖️✈️   📞 +91 9999779870 | ✉️ hello@ifytravel.com";

try {
    // Check if key exists
    $exists = $db->fetch("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);

    if ($exists) {
        $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        echo "Updated existing setting.\n";
    } else {
        $db->execute("INSERT INTO site_settings (setting_key, setting_value, description) VALUES (?, ?, ?)", [$key, $value, 'Hero Subtitle']);
        echo "Inserted new setting.\n";
    }
    echo "Hero Subtitle Updated Successfully: $value\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>