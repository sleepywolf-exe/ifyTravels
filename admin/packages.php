<?php
// admin/packages.php
require 'auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
$db = Database::getInstance();
// Auto-Migration: Ensure trust_badges column exists
try {
    $db->getConnection()->query("SELECT trust_badges FROM packages LIMIT 1");
} catch (Exception $e) {
    try {
        $db->getConnection()->exec("ALTER TABLE packages ADD COLUMN trust_badges TEXT DEFAULT NULL");
    } catch (Exception $e2) {
        // Silent fail or log
    }
}

$message = '';
$error = '';

// Helper to clean up image files
function deleteOldImage($imageUrl)
{
    if (empty($imageUrl))
        return;
    $filePath = __DIR__ . '/../' . $imageUrl;
    if (strpos($imageUrl, 'assets/images/packages/') === 0 && file_exists($filePath)) {
        unlink($filePath);
    }
}

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    $title = trim($_POST['title']);
    $destination_id = $_POST['destination_id'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    // Validation
    if (empty($title))
        $error = "Title is required.";
    if (empty($destination_id))
        $error = "Destination is required.";
    if (empty($price))
        $error = "Price is required.";

    // Handle JSON fields (converting newlines to array)
    $features = array_filter(array_map('trim', explode("\n", $_POST['features'])));
    $inclusions = array_filter(array_map('trim', explode("\n", $_POST['inclusions'])));
    $exclusions = array_filter(array_map('trim', explode("\n", $_POST['exclusions'])));

    // Handle Activities
    $activities = $_POST['activities'] ?? [];
    if (!empty($_POST['activities_other'])) {
        $others = array_map('trim', explode(',', $_POST['activities_other']));
        $activities = array_merge($activities, $others);
    }
    $activities = array_unique(array_filter($activities));

    // Handle Themes
    $themes = $_POST['themes'] ?? [];
    if (!empty($_POST['themes_other'])) {
        $others = array_map('trim', explode(',', $_POST['themes_other']));
        $themes = array_merge($themes, $others);
    }
    $themes = array_unique(array_filter($themes));

    // Handle Trust Badges
    $trust_badges = $_POST['trust_badges'] ?? [];
    $trust_badges_json = json_encode(array_values($trust_badges));

    $features_json = json_encode(array_values($features));
    $inclusions_json = json_encode(array_values($inclusions));
    $exclusions_json = json_encode(array_values($exclusions));
    $activities_json = json_encode(array_values($activities));
    $themes_json = json_encode(array_values($themes));

    // Auto-generate slug
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = generateSlug($title);
    }

    // Manual Destination Covered Field
    $destination_covered = trim($_POST['destination_covered'] ?? '');

    // Handle Image Logic
    $image_url = $_POST['existing_image_url'] ?? '';

    if (empty($error)) {
        if (!empty($_POST['image_url_input'])) {
            // Deleting old image if replacing with URL
            if (!empty($id)) {
                $oldRec = $db->fetch("SELECT image_url FROM packages WHERE id = ?", [$id]);
                if ($oldRec && $oldRec['image_url'] !== $_POST['image_url_input']) {
                    deleteOldImage($oldRec['image_url']);
                }
            }
            $image_url = trim($_POST['image_url_input']);
        }

        if (isset($_FILES['image_file'])) {
            if ($_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/packages/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                    // Delete old image
                    if (!empty($id)) {
                        $oldRec = $db->fetch("SELECT image_url FROM packages WHERE id = ?", [$id]);
                        if ($oldRec) {
                            deleteOldImage($oldRec['image_url']);
                        }
                    }
                    $image_url = 'assets/images/packages/' . $fileName;
                } else {
                    $error = "Failed to upload image. Verify directory permissions.";
                }
            } elseif ($_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle specific errors
                switch ($_FILES['image_file']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error = "Image is too large. Server limit: " . ini_get('upload_max_filesize');
                        break;
                    default:
                        $error = "Upload failed with error code: " . $_FILES['image_file']['error'];
                        break;
                }
            }
        }
    }

    if (empty($error)) {
        try {
            if ($action === 'update' && !empty($id)) {
                $db->execute(
                    "UPDATE packages SET title=?, slug=?, destination_id=?, price=?, duration=?, description=?, image_url=?, is_popular=?, is_new=?, features=?, inclusions=?, exclusions=?, activities=?, themes=?, trust_badges=?, destination_covered=? WHERE id=?",
                    [$title, $slug, $destination_id, $price, $duration, $description, $image_url, $is_popular, $is_new, $features_json, $inclusions_json, $exclusions_json, $activities_json, $themes_json, $trust_badges_json, $destination_covered, $id]
                );
                $message = "Package updated successfully!";
            } else {
                $db->execute(
                    "INSERT INTO packages (title, slug, destination_id, price, duration, description, image_url, is_popular, is_new, features, inclusions, exclusions, activities, themes, trust_badges, destination_covered) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$title, $slug, $destination_id, $price, $duration, $description, $image_url, $is_popular, $is_new, $features_json, $inclusions_json, $exclusions_json, $activities_json, $themes_json, $trust_badges_json, $destination_covered]
                );
                $message = "Package created successfully!";
            }
        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $rec = $db->fetch("SELECT image_url FROM packages WHERE id = ?", [$id]);
    if ($rec) {
        deleteOldImage($rec['image_url']);
    }
    $db->execute("DELETE FROM packages WHERE id = ?", [$id]);
    redirect('packages.php');
}

// Fetch record for editing
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $db->query("SELECT * FROM packages WHERE id = ?", [$_GET['edit']]);
    $editData = $stmt->fetch();
}

$packages = $db->fetchAll("SELECT p.*, d.name as destination_name FROM packages p LEFT JOIN destinations d ON p.destination_id = d.id ORDER BY p.created_at DESC");
$destinations = $db->fetchAll("SELECT id, name FROM destinations ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Packages - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Custom Quill Toolbar */
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #d1d5db;
        }

        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #d1d5db;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen overflow-hidden">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <?php echo $editData ? 'Edit Package' : 'Manage Packages'; ?>
            </h1>
            <?php if ($editData): ?>
                <button onclick="openModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md transition">
                    + Add Package
                </button>
            <?php endif; ?>
        </header>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6"><?php echo e($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6"><?php echo e($error); ?></div>
        <?php endif; ?>

        <!-- Modal Background -->
        <div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300">
        </div>

        <!-- Modal Content (Drawer style for more space) -->
        <div id="formModal"
            class="fixed inset-y-0 right-0 w-full md:w-2/5 bg-white shadow-2xl z-50 transform transition-transform duration-300 translate-x-full overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        <?php echo $editData ? 'Edit Package' : 'Add New Package'; ?>
                    </h2>
                    <a href="packages.php" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
                <form method="POST" enctype="multipart/form-data"
                    action="packages.php<?php echo $editData ? '?edit=' . $editData['id'] : ''; ?>">
                    <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                    <?php endif; ?>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" required value="<?php echo e($editData['title'] ?? ''); ?>"
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Destination</label>
                            <select name="destination_id" required
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg">
                                <option value="">Select Destination</option>
                                <?php foreach ($destinations as $dest): ?>
                                    <option value="<?php echo $dest['id']; ?>" <?php echo ($editData && $editData['destination_id'] == $dest['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($dest['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Destination Covered (Specific
                                Cities)</label>
                            <input type="text" name="destination_covered"
                                value="<?php echo e($editData['destination_covered'] ?? ''); ?>"
                                placeholder="e.g. Baku, Qusar, Qabala"
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Price (₹)</label>
                                <input type="number" name="price" required
                                    value="<?php echo e($editData['price'] ?? ''); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Duration</label>
                                <input type="text" name="duration" placeholder="e.g. 5 Days / 4 Nights" required
                                    value="<?php echo e($editData['duration'] ?? ''); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg">
                            </div>
                        </div>

                        <?php if ($editData): ?>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
                                <input type="text" name="slug" value="<?php echo e($editData['slug'] ?? ''); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg bg-gray-50">
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_popular" id="is_popular" value="1" <?php echo ($editData && $editData['is_popular']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 rounded">
                            <label for="is_popular" class="text-sm font-bold text-gray-700">Mark as Popular / Top
                                Priority</label>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_new" id="is_new" value="1" <?php echo ($editData && !empty($editData['is_new'])) ? 'checked' : ''; ?>
                                class="w-4 h-4 text-green-600 rounded">
                            <label for="is_new" class="text-sm font-bold text-gray-700">Mark as NEW (Top of
                                List)</label>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                            <!-- Hidden input to store Quill's HTML content -->
                            <input type="hidden" name="description"
                                value="<?php echo e($editData['description'] ?? ''); ?>">
                            <!-- Quill editor container -->
                            <div id="editor-container" class="h-48 bg-white border border-gray-300 rounded-lg">
                                <?php echo $editData['description'] ?? ''; ?>
                            </div>
                        </div>

                        <!-- New: Activities & Themes -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tour Activities</label>
                                <div class="grid grid-cols-1 gap-2 text-sm">
                                    <?php
                                    $allActivities = ['Cable Car', 'Museums', 'Sightseeing', 'Boating', 'Trekking', 'Shopping', 'Cultural Show', 'Wildlife Safari', 'Beach Activities'];
                                    $currentActivities = isset($editData['activities']) ? json_decode($editData['activities'], true) : [];
                                    if (!is_array($currentActivities))
                                        $currentActivities = [];

                                    foreach ($allActivities as $act) {
                                        $checked = in_array($act, $currentActivities) ? 'checked' : '';
                                        echo "<label class='flex items-center space-x-2'><input type='checkbox' name='activities[]' value='$act' $checked class='rounded text-blue-600'> <span>$act</span></label>";
                                    }
                                    ?>
                                </div>
                                <input type="text" name="activities_other" placeholder="Other (comma separated)"
                                    class="mt-2 w-full border border-gray-300 rounded px-2 py-1 text-xs">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tour Themes</label>
                                <div class="grid grid-cols-1 gap-2 text-sm">
                                    <?php
                                    $allThemes = ['Adventure Tours', 'Hill Stations & Valleys', 'Culture & Heritage', 'Architecture & Gardens', 'Honeymoon', 'Family', 'Religious', 'Beach', 'Luxury'];
                                    $currentThemes = isset($editData['themes']) ? json_decode($editData['themes'], true) : [];
                                    if (!is_array($currentThemes))
                                        $currentThemes = [];

                                    foreach ($allThemes as $theme) {
                                        $checked = in_array($theme, $currentThemes) ? 'checked' : '';
                                        echo "<label class='flex items-center space-x-2'><input type='checkbox' name='themes[]' value='$theme' $checked class='rounded text-purple-600'> <span>$theme</span></label>";
                                    }
                                    ?>
                                </div>
                                <input type="text" name="themes_other" placeholder="Other (comma separated)"
                                    class="mt-2 w-full border border-gray-300 rounded px-2 py-1 text-xs">
                            </div>
                        </div>

                        <!-- Trust Badges (Trust Indicators) -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Trust Badges (Displayed on
                                Detail Page)</label>
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                <?php
                                $allBadges = [
                                    'secure_payment' => 'Secure Payment Gateway',
                                    'customer_support' => '24/7 Customer Support',
                                    'free_cancellation' => 'Free Cancellation (7 days prior)',
                                    'verified_operator' => 'Verified Operator',
                                    'best_price' => 'Best Price Guarantee'
                                ];
                                $currentBadges = isset($editData['trust_badges']) ? json_decode($editData['trust_badges'], true) : [];
                                if (!is_array($currentBadges))
                                    $currentBadges = [];

                                foreach ($allBadges as $key => $label) {
                                    $checked = in_array($key, $currentBadges) ? 'checked' : '';
                                    echo "<label class='flex items-center space-x-2'>
                                                <input type='checkbox' name='trust_badges[]' value='$key' $checked class='rounded text-green-600'> 
                                                <span>$label</span>
                                              </label>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- JSON Fields -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Features (One per
                                line)</label>
                            <textarea name="features" rows="3"
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm"><?php
                                if (isset($editData['features'])) {
                                    $arr = json_decode($editData['features'], true);
                                    echo is_array($arr) ? implode("\n", $arr) : '';
                                }
                                ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Inclusions (One
                                    line)</label>
                                <textarea name="inclusions" rows="3"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm"><?php
                                    if (isset($editData['inclusions'])) {
                                        $arr = json_decode($editData['inclusions'], true);
                                        echo is_array($arr) ? implode("\n", $arr) : '';
                                    }
                                    ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Exclusions (One
                                    line)</label>
                                <textarea name="exclusions" rows="3"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm"><?php
                                    if (isset($editData['exclusions'])) {
                                        $arr = json_decode($editData['exclusions'], true);
                                        echo is_array($arr) ? implode("\n", $arr) : '';
                                    }
                                    ?></textarea>
                            </div>
                        </div>

                        <!-- Image Management -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Package Image</label>
                            <?php if (!empty($editData['image_url'])): ?>
                                <div class="mb-3">
                                    <img src="<?php echo base_url($editData['image_url']); ?>"
                                        class="h-24 w-full object-cover rounded shadow-sm">
                                    <input type="hidden" name="existing_image_url"
                                        value="<?php echo e($editData['image_url']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="text-xs font-semibold text-gray-600 block mb-1">Option A: Image
                                    URL</label>
                                <input type="text" name="image_url_input" placeholder="https://..."
                                    class="w-full border border-gray-300 px-3 py-2 rounded text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block mb-1">Option B: Upload
                                    File</label>
                                <input type="file" name="image_file" accept="image/*"
                                    class="w-full text-sm text-gray-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-bold hover:bg-blue-700 shadow-md">
                            <?php echo $editData ? 'Save Changes' : 'Create Package'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- List -->
        <div class="w-full">

            <!-- List -->

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b">
                            <tr>
                                <th class="px-6 py-4">Image</th>
                                <th class="px-6 py-4">Title / Dest</th>
                                <th class="px-6 py-4">Price</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($packages as $p): ?>
                                <tr
                                    class="hover:bg-blue-50 transition <?php echo ($editData && $editData['id'] == $p['id']) ? 'bg-blue-50 ring-2 ring-inset ring-blue-100' : ''; ?>">
                                    <td class="px-6 py-4">
                                        <div class="w-16 h-12 rounded-lg bg-gray-200 bg-cover bg-center shadow-sm"
                                            style="background-image: url('<?php echo base_url($p['image_url'] ?? ''); ?>')">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-base"><?php echo e($p['title']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e($p['destination_name'] ?? 'N/A'); ?>
                                            • <?php echo e($p['duration']); ?>
                                            <?php if ($p['is_popular']): ?><span class="text-yellow-600 ml-1">★
                                                    Popular</span><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-green-700">
                                        ₹<?php echo number_format($p['price']); ?></td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="?edit=<?php echo $p['id']; ?>"
                                            class="inline-block text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1 rounded-full text-xs font-bold transition">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $p['id']; ?>"
                                            class="inline-block text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-full text-xs font-bold transition"
                                            onclick="return confirm('Delete this package?')">
                                            <i class="fas fa-trash-alt mr-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Write a beautiful description...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'clean']
                ]
            }
        });

        // Form Submission Handler
        document.querySelector('form').onsubmit = function () {
            var description = document.querySelector('input[name=description]');
            description.value = quill.root.innerHTML;
        };
        // Set initial content for Quill from the hidden input
        var initialDescription = document.querySelector('input[name=description]').value;
        if (initialDescription) {
            quill.root.innerHTML = initialDescription;
        }
        document.querySelector('form').onsubmit = function () {
            var descriptionInput = document.querySelector('input[name=description]');
            descriptionInput.value = quill.root.innerHTML;
        };

        // Modal Logic
        const modalOverlay = document.getElementById('modalOverlay');
        const formModal = document.getElementById('formModal');
        const body = document.body;

        function openModal() {
            modalOverlay.classList.remove('hidden');
            formModal.classList.remove('translate-x-full');
            body.classList.add('overflow-hidden');
        }

        // Auto-open if Editing or Error
        <?php if ($editData || $error): ?>
            openModal();
        <?php endif; ?>
    </script>
</body>

</html>