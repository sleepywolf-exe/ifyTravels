<?php
require 'auth_check.php';

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Handle Text Settings
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        }
    }

    // 2. Handle File Uploads
    $uploadDir = '../assets/images/uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/svg+xml'];

    $fileFields = ['site_logo', 'site_favicon', 'hero_bg', 'contact_bg', 'destinations_bg', 'packages_bg'];

    foreach ($fileFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES[$field]['tmp_name'];
            $fileName = basename($_FILES[$field]['name']);
            $fileType = $_FILES[$field]['type'];

            if (in_array($fileType, $allowedTypes)) {
                // Generate unique name to avoid cache issues or overwrites
                $newFileName = $field . '_' . time() . '_' . $fileName;
                $targetPath = $uploadDir . $newFileName;

                // Remove old file if exists (optional, strictly speaking we should query DB first to find old file)
                // For now, just save new file.

                if (move_uploaded_file($fileTmp, $targetPath)) {
                    // Save relative path to DB
                    $dbPath = 'assets/images/uploads/' . $newFileName;

                    // Insert or Update
                    // Check if key exists first
                    $exists = $db->fetch("SELECT id FROM site_settings WHERE setting_key = ?", [$field]);
                    if ($exists) {
                        $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$dbPath, $field]);
                    } else {
                        $db->execute("INSERT INTO site_settings (setting_key, setting_value, description) VALUES (?, ?, ?)", [$field, $dbPath, 'Uploaded Image']);
                    }
                }
            }
        }
    }

    $message = "Settings and Images updated successfully!";

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

        <form method="POST" enctype="multipart/form-data"
            class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-4xl">
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-700 border-b pb-2">Branding & Images</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Site Logo</label>
                        <?php if (!empty($settingsMap['site_logo'])): ?>
                            <div class="mb-2 p-2 bg-gray-100 rounded flex items-center justify-center">
                                <img src="../<?php echo e($settingsMap['site_logo']); ?>" class="h-12 object-contain">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="site_logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-400 mt-1">Recommended: Transparent PNG</p>
                    </div>

                    <!-- Favicon -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Favicon</label>
                        <?php if (!empty($settingsMap['site_favicon'])): ?>
                            <div class="mb-2 p-2 bg-gray-100 rounded w-16 h-16 flex items-center justify-center">
                                <img src="../<?php echo e($settingsMap['site_favicon']); ?>" class="w-8 h-8 object-contain">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="site_favicon" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Hero BG -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Hero Background (Home)</label>
                        <?php if (!empty($settingsMap['hero_bg'])): ?>
                            <img src="../<?php echo e($settingsMap['hero_bg']); ?>"
                                class="h-20 w-full object-cover rounded mb-2 opacity-80">
                        <?php endif; ?>
                        <input type="file" name="hero_bg" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Contact BG -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contact Header Image</label>
                        <?php if (!empty($settingsMap['contact_bg'])): ?>
                            <img src="../<?php echo e($settingsMap['contact_bg']); ?>"
                                class="h-20 w-full object-cover rounded mb-2 opacity-80">
                        <?php endif; ?>
                        <input type="file" name="contact_bg" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Destinations BG -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Destinations Header Image</label>
                        <?php if (!empty($settingsMap['destinations_bg'])): ?>
                            <img src="../<?php echo e($settingsMap['destinations_bg']); ?>"
                                class="h-20 w-full object-cover rounded mb-2 opacity-80">
                        <?php endif; ?>
                        <input type="file" name="destinations_bg" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Packages BG -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Packages Header Image</label>
                        <?php if (!empty($settingsMap['packages_bg'])): ?>
                            <img src="../<?php echo e($settingsMap['packages_bg']); ?>"
                                class="h-20 w-full object-cover rounded mb-2 opacity-80">
                        <?php endif; ?>
                        <input type="file" name="packages_bg" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                </div>

                <div class="border-t pb-2"></div>

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