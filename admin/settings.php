<?php
require 'auth_check.php';

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF Token");
    }

    // 1. Handle Text Settings
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        }
    }

    // 2. Handle File Uploads
    $uploadDir = '../assets/images/uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/svg+xml'];

    $fileFields = ['site_logo', 'site_favicon', 'hero_bg', 'contact_bg', 'destinations_bg', 'packages_bg', 'og_image'];

    // Special Handling for Service Account Logic
    if (isset($_FILES['service_account_json']) && $_FILES['service_account_json']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['service_account_json']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['service_account_json']['name'], PATHINFO_EXTENSION));

        if ($ext === 'json') {
            $targetDir = __DIR__ . '/../includes/config/';
            if (!is_dir($targetDir))
                mkdir($targetDir, 0755, true);

            if (move_uploaded_file($tmpName, $targetDir . 'service_account.json')) {
                $message .= "Service Account Key updated successfully! ";
            } else {
                $message .= "Failed to save Service Account Key. ";
            }
        } else {
            $message .= "Invalid file type. Only .json allowed for Service Account Key. ";
        }
    }

    // Special Handling for Service Account Logic
    if (isset($_FILES['service_account_json']) && $_FILES['service_account_json']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['service_account_json']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['service_account_json']['name'], PATHINFO_EXTENSION));

        if ($ext === 'json') {
            $targetDir = __DIR__ . '/../includes/config/';
            if (!is_dir($targetDir))
                mkdir($targetDir, 0755, true);

            if (move_uploaded_file($tmpName, $targetDir . 'service_account.json')) {
                $message .= "Service Account Key updated successfully! ";
            } else {
                $message .= "Failed to save Service Account Key. ";
            }
        } else {
            $message .= "Invalid file type. Only .json allowed for Service Account Key. ";
        }
    }

    foreach ($fileFields as $field) {
        if (isset($_FILES[$field])) {
            if ($_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                // ... Existing logic ...
                $fileTmp = $_FILES[$field]['tmp_name'];
                $fileName = basename($_FILES[$field]['name']);
                $fileType = $_FILES[$field]['type'];

                if (in_array($fileType, $allowedTypes)) {
                    $newFileName = $field . '_' . time() . '_' . $fileName;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        $dbPath = 'assets/images/uploads/' . $newFileName;

                        // Check if key exists first
                        $exists = $db->fetch("SELECT id FROM site_settings WHERE setting_key = ?", [$field]);
                        if ($exists) {
                            $db->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$dbPath, $field]);
                        } else {
                            $db->execute("INSERT INTO site_settings (setting_key, setting_value, description) VALUES (?, ?, ?)", [$field, $dbPath, 'Uploaded Image']);
                        }
                    } else {
                        $message .= " Failed to move uploaded file for $field. Check permissions. ";
                    }
                } else {
                    $message .= " Invalid file type for $field. Allowed: JPG, PNG, WEBP, SVG. ";
                }
            } elseif ($_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle Errors
                $errorCode = $_FILES[$field]['error'];
                $errorMsg = "Unknown Error";
                switch ($errorCode) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errorMsg = "File too large (exceeds server limit of " . ini_get('upload_max_filesize') . ")";
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMsg = "File too large (exceeds form limit)";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMsg = "File only partially uploaded";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errorMsg = "Missing temporary folder";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errorMsg = "Failed to write file to disk";
                        break;
                }
                $message .= " Error uploading $field: $errorMsg. ";
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
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        font-family: 'Outfit',
        sans-serif;
    </style>
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" border-blue-600 text-blue-600", " border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className = evt.currentTarget.className.replace(" border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300", " border-blue-600 text-blue-600");
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Open the first tab by default
            document.querySelector('.tab-link').click();
        });

        function previewImage(input, previewIdSuffix) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = document.getElementById('preview_' + previewIdSuffix);
                    var placeholder = document.getElementById('placeholder_' + previewIdSuffix);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }

                    // Update button text or filename if elements exist
                    var fn = document.getElementById('filename_' + previewIdSuffix);
                    if (fn) fn.textContent = input.files[0].name;

                    // Remove fallback badge if previewing favicon
                    if (previewIdSuffix === 'site_favicon') {
                        var badge = document.getElementById('fallback_badge');
                        if (badge) badge.style.display = 'none';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>

<body class="bg-gray-50 flex h-screen text-gray-800">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Site Settings</h1>
                <p class="text-gray-500 mt-2 text-lg font-light">Configure your website's core details</p>
            </div>
        </header>

        <?php if ($message): ?>
            <div
                class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl mb-8 shadow-sm border border-emerald-100 flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data"
            class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 relative">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <!-- Tab Headers -->
            <div class="border-b border-gray-200 px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button type="button" onclick="openTab(event, 'General')"
                        class="tab-link border-blue-600 text-blue-600 whitespace-nowrap py-6 border-b-2 font-medium text-sm transition-colors">
                        General & Contact
                    </button>
                    <button type="button" onclick="openTab(event, 'Images')"
                        class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-6 border-b-2 font-medium text-sm transition-colors">
                        Branding & Images
                    </button>
                    <button type="button" onclick="openTab(event, 'HomeContent')"
                        class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-6 border-b-2 font-medium text-sm transition-colors">
                        Home Content
                    </button>
                    <button type="button" onclick="openTab(event, 'SEO')"
                        class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-6 border-b-2 font-medium text-sm transition-colors">
                        Analytics & SEO
                    </button>
                </nav>
            </div>

            <div class="p-8 lg:p-10">
                <!-- Tab: General -->
                <div id="General" class="tab-content space-y-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Website Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Website Name</label>
                                <input type="text" name="site_name"
                                    value="<?php echo e($settingsMap['site_name'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title"
                                    value="<?php echo e($settingsMap['hero_title'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div class="col-span-full">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Subtitle</label>
                                <!-- Quill Editor Container -->
                                <div id="editor-hero-subtitle" class="bg-white rounded-xl" style="height: 150px;">
                                    <?php echo $settingsMap['hero_subtitle'] ?? ''; ?>
                                </div>
                                <input type="hidden" name="hero_subtitle" id="hero_subtitle">
                            </div>
                        </div>
                    </div>

                    <!-- Quill JS & Init -->
                    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var quill = new Quill('#editor-hero-subtitle', {
                                theme: 'snow',
                                modules: {
                                    toolbar: [
                                        ['bold', 'italic', 'underline'],
                                        [{ 'color': [] }, { 'background': [] }],
                                        ['clean']
                                    ]
                                }
                            });

                            // Sync content on form submit
                            var form = document.querySelector('form');
                            form.onsubmit = function () {
                                var content = document.querySelector('input[name=hero_subtitle]');
                                content.value = quill.root.innerHTML;
                            };
                        });
                    </script>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Email</label>
                                <input type="email" name="contact_email"
                                    value="<?php echo e($settingsMap['contact_email'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="text" name="contact_phone"
                                    value="<?php echo e($settingsMap['contact_phone'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                <input type="text" name="address"
                                    value="<?php echo e($settingsMap['address'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Social Media</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
                                <input type="text" name="social_facebook"
                                    value="<?php echo e($settingsMap['social_facebook'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter URL</label>
                                <input type="text" name="social_twitter"
                                    value="<?php echo e($settingsMap['social_twitter'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                                <input type="text" name="social_instagram"
                                    value="<?php echo e($settingsMap['social_instagram'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Footer Content</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Description</label>
                                <textarea name="footer_description" rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"><?php echo e($settingsMap['footer_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Text</label>
                                    <input type="text" name="footer_payment_text"
                                        value="<?php echo e($settingsMap['footer_payment_text'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Copyright Text</label>
                                    <input type="text" name="footer_copyright"
                                        value="<?php echo e($settingsMap['footer_copyright'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Images -->
                <div id="Images" class="tab-content hidden space-y-10">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-8 flex items-start">
                        <span class="text-2xl mr-4">ℹ️</span>
                        <p class="text-blue-800 text-sm">Upload transparent PNGs for logos. High-quality JPEGs/WebP (min
                            1920px wide) recommended for backgrounds.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Logo -->
                        <div
                            class="p-6 border border-dashed border-gray-200 rounded-2xl bg-gray-50/50 flex flex-col items-center text-center">
                            <label class="block text-sm font-bold text-gray-800 mb-4">Site Logo</label>

                            <!-- Preview Container -->
                            <div
                                class="relative w-48 h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center mb-4 overflow-hidden group">
                                <?php if (!empty($settingsMap['site_logo'])): ?>
                                    <img id="preview_site_logo" src="../<?php echo e($settingsMap['site_logo']); ?>"
                                        class="max-h-full max-w-full object-contain">
                                <?php else: ?>
                                    <img id="preview_site_logo" class="hidden max-h-full max-w-full object-contain">
                                    <span class="text-gray-400 text-xs" id="placeholder_site_logo">No Logo</span>
                                <?php endif; ?>
                            </div>

                            <!-- Styled File Input -->
                            <label
                                class="cursor-pointer bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm transition-all focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                                <span id="btn_text_site_logo">Choose New Logo</span>
                                <input type="file" name="site_logo" accept="image/*" class="hidden"
                                    onchange="previewImage(this, 'site_logo')">
                            </label>
                            <p class="text-xs text-gray-400 mt-2">Recommended: PNG / SVG</p>
                        </div>

                        <!-- Favicon -->
                        <div
                            class="p-6 border border-dashed border-gray-200 rounded-2xl bg-gray-50/50 flex flex-col items-center text-center">
                            <label class="block text-sm font-bold text-gray-800 mb-4">Favicon</label>

                            <?php
                            $favSrc = $settingsMap['site_favicon'] ?? '';
                            $isFallback = false;
                            if (empty($favSrc) && !empty($settingsMap['site_logo'])) {
                                $favSrc = $settingsMap['site_logo'];
                                $isFallback = true;
                            }
                            ?>

                            <!-- Preview Container -->
                            <div
                                class="relative w-32 h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center mb-4 overflow-hidden">
                                <?php if (!empty($favSrc)): ?>
                                    <img id="preview_site_favicon" src="../<?php echo e($favSrc); ?>"
                                        class="max-h-16 w-auto object-contain">
                                <?php else: ?>
                                    <img id="preview_site_favicon" class="hidden max-h-16 w-auto object-contain">
                                    <span class="text-gray-400 text-xs" id="placeholder_site_favicon">No Favicon</span>
                                <?php endif; ?>

                                <?php if ($isFallback): ?>
                                    <div id="fallback_badge" class="absolute bottom-2 left-1/2 transform -translate-x-1/2">
                                        <span
                                            class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100 whitespace-nowrap">Using
                                            Logo</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Styled File Input -->
                            <label
                                class="cursor-pointer bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm transition-all focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                                <span id="btn_text_site_favicon">Upload Custom Favicon</span>
                                <input type="file" name="site_favicon" accept="image/*" class="hidden"
                                    onchange="previewImage(this, 'site_favicon')">
                            </label>
                            <p class="text-xs text-gray-400 mt-2">Recommended: 32x32 or 64x64 PNG</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Page Hero Backgrounds</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php
                            $bgFields = [
                                'hero_bg' => 'Home Hero BG',
                                'contact_bg' => 'Contact Page BG',
                                'destinations_bg' => 'Destinations Page BG',
                                'packages_bg' => 'Packages Page BG'
                            ];
                            foreach ($bgFields as $key => $label): ?>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2"><?php echo $label; ?></label>
                                    <div
                                        class="relative group mb-3 w-full h-40 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center">
                                        <?php if (!empty($settingsMap[$key])): ?>
                                            <img id="preview_<?php echo $key; ?>" src="../<?php echo e($settingsMap[$key]); ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img id="preview_<?php echo $key; ?>" class="hidden w-full h-full object-cover">
                                            <span class="text-gray-400 text-xs">No Image</span>
                                        <?php endif; ?>

                                        <!-- Overlay Button -->
                                        <label
                                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                            <span
                                                class="bg-white text-gray-800 font-bold py-2 px-4 rounded-lg shadow-lg transform scale-95 group-hover:scale-100 transition-transform">Change
                                                Image</span>
                                            <input type="file" name="<?php echo $key; ?>" accept="image/*" class="hidden"
                                                onchange="previewImage(this, '<?php echo $key; ?>')">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 text-right truncate" id="filename_<?php echo $key; ?>">
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Home Content -->
                <div id="HomeContent" class="tab-content hidden space-y-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Live Stats Bar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Luxury Trips</label>
                                <input type="text" name="stats_trips_count"
                                    value="<?php echo e($settingsMap['stats_trips_count'] ?? '500+'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">5-Star Reviews</label>
                                <input type="text" name="stats_reviews_count"
                                    value="<?php echo e($settingsMap['stats_reviews_count'] ?? '98%'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Destinations</label>
                                <input type="text" name="stats_destinations_count"
                                    value="<?php echo e($settingsMap['stats_destinations_count'] ?? '50+'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Concierge Text</label>
                                <input type="text" name="stats_concierge_text"
                                    value="<?php echo e($settingsMap['stats_concierge_text'] ?? '24/7'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Newsletter Section</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Section Heading</label>
                                <input type="text" name="newsletter_heading"
                                    value="<?php echo e($settingsMap['newsletter_heading'] ?? 'Join the Elite Club'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Helper Text</label>
                                <textarea name="newsletter_text" rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"><?php echo e($settingsMap['newsletter_text'] ?? 'Subscribe to receive exclusive offers, travel inspiration, and member-only perks directly to your inbox.'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: SEO -->
                <div id="SEO" class="tab-content hidden space-y-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Tracking IDs</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Google Analytics
                                    (G-XXXX)</label>
                                <input type="text" name="google_analytics_id"
                                    value="<?php echo e($settingsMap['google_analytics_id'] ?? ''); ?>"
                                    placeholder="G-XXXXXXXXXX"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-mono text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Meta Pixel ID</label>
                                <input type="text" name="meta_pixel_id"
                                    value="<?php echo e($settingsMap['meta_pixel_id'] ?? ''); ?>"
                                    placeholder="1234567890"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Google Indexing API Key (JSON)</label>
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-blue-900 font-bold mb-1">Service Account Key</h4>
                                    <p class="text-xs text-blue-700 mb-3">Required for Instant Indexing. Upload the
                                        <code>service_account.json</code> file from Google Cloud Console.
                                    </p>
                                    <?php
                                    $keyPath = __DIR__ . '/../includes/config/service_account.json';
                                    if (file_exists($keyPath)): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Key Installed
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Key Missing
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-shrink-0">
                                    <label
                                        class="cursor-pointer bg-white text-blue-600 font-bold py-2 px-4 rounded-lg shadow-sm border border-blue-200 hover:bg-blue-50 transition text-sm">
                                        Upload JSON
                                        <input type="file" name="service_account_json" accept=".json" class="hidden"
                                            onchange="if(confirm('Upload new Service Account Key?')) this.form.submit();">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Meta Tags</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Default Meta
                                    Description</label>
                                <textarea name="meta_description" rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"><?php echo e($settingsMap['meta_description'] ?? ''); ?></textarea>
                                <p class="text-xs text-gray-400 mt-2 text-right">Recommended: 150-160 characters</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" name="meta_keywords"
                                    value="<?php echo e($settingsMap['meta_keywords'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Social Share Image (Open
                            Graph)</label>
                        <div
                            class="relative group mb-3 w-full max-w-md h-48 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center">
                            <?php if (!empty($settingsMap['og_image'])): ?>
                                <img id="preview_og_image" src="../<?php echo e($settingsMap['og_image']); ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <img id="preview_og_image" class="hidden w-full h-full object-cover">
                                <span class="text-gray-400 text-xs">No OG Image</span>
                            <?php endif; ?>

                            <!-- Overlay Button -->
                            <label
                                class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span
                                    class="bg-white text-gray-800 font-bold py-2 px-4 rounded-lg shadow-lg transform scale-95 group-hover:scale-100 transition-transform">Change
                                    Image</span>
                                <input type="file" name="og_image" accept="image/*" class="hidden"
                                    onchange="previewImage(this, 'og_image')">
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sticky Footer Action Bar -->
            <div class="bg-gray-50 px-8 py-5 rounded-b-3xl border-t border-gray-200 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 transform hover:-translate-y-0.5">
                    Save All Changes
                </button>
            </div>
        </form>
    </main>
</body>

</html>