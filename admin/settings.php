<?php
require 'auth_check.php';

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        }
    }
    $message = "Settings updated successfully!";

    // Clear settings cache
    global $globalSettings;
    $globalSettings = null;
}

$settings = $db->fetchAll("SELECT * FROM site_settings");
$settingsMap = [];
foreach ($settings as $s) {
    $settingsMap[$s['setting_key']] = $s['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Site Settings - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Site Settings</h1>
        </header>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6"><?php echo e($message); ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-4xl">
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-700 border-b pb-2">General Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Website Name</label>
                        <input type="text" name="site_name" value="<?php echo e($settingsMap['site_name'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-700 border-b pb-2 pt-4">Hero Section</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hero Title</label>
                    <input type="text" name="hero_title" value="<?php echo e($settingsMap['hero_title'] ?? ''); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="2"
                        class="w-full px-4 py-2 border rounded-lg"><?php echo e($settingsMap['hero_subtitle'] ?? ''); ?></textarea>
                </div>

                <h3 class="text-xl font-bold text-gray-700 border-b pb-2 pt-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email"
                            value="<?php echo e($settingsMap['contact_email'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="contact_phone"
                            value="<?php echo e($settingsMap['contact_phone'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                        <input type="text" name="address" value="<?php echo e($settingsMap['address'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-700 border-b pb-2 pt-4">Social Links</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Facebook URL</label>
                        <input type="text" name="social_facebook"
                            value="<?php echo e($settingsMap['social_facebook'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Twitter URL</label>
                        <input type="text" name="social_twitter"
                            value="<?php echo e($settingsMap['social_twitter'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Instagram URL</label>
                        <input type="text" name="social_instagram"
                            value="<?php echo e($settingsMap['social_instagram'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-700 border-b pb-2 pt-4">Footer Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Footer Description</label>
                        <textarea name="footer_description" rows="3"
                            class="w-full px-4 py-2 border rounded-lg"><?php echo e($settingsMap['footer_description'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Payment Text</label>
                        <input type="text" name="footer_payment_text"
                            value="<?php echo e($settingsMap['footer_payment_text'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Copyright Text</label>
                        <input type="text" name="footer_copyright"
                            value="<?php echo e($settingsMap['footer_copyright'] ?? ''); ?>"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Save
                    Changes</button>
            </div>
        </form>
    </main>
</body>

</html>