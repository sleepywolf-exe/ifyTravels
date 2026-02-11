<?php
// admin/destinations.php
require 'auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
$db = Database::getInstance();
$message = '';
$error = '';

// Helper to clean up image files
function deleteOldImage($imageUrl)
{
    if (empty($imageUrl))
        return;
    $filePath = __DIR__ . '/../' . $imageUrl;
    if (strpos($imageUrl, 'assets/images/destinations/') === 0 && file_exists($filePath)) {
        unlink($filePath);
    }
}

// Lazy Migration for Map Embed
function ensureMapColumnExists($db)
{
    try {
        $db->fetch("SELECT map_embed FROM destinations LIMIT 1");
    } catch (Exception $e) {
        try {
            $db->execute("ALTER TABLE destinations ADD COLUMN map_embed TEXT");
        } catch (Exception $ex) {
            // Ignore if it fails (e.g. read only), but ideally log it
        }
    }

    // Auto-Migration: Ensure search_intent column exists
    try {
        $db->fetch("SELECT search_intent FROM destinations LIMIT 1");
    } catch (Exception $e) {
        try {
            $db->execute("ALTER TABLE destinations ADD COLUMN search_intent VARCHAR(50) DEFAULT 'Informational'");
        } catch (Exception $e2) {
            // Silent fail
        }
    }
}
ensureMapColumnExists($db);

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);
    $type = $_POST['type'];
    $description = $_POST['description'];

    // Validation
    if (empty($name))
        $error = "Destination Name is required.";
    if (empty($country))
        $error = "Country is required.";
    if (empty($action))
        $error = "Invalid form submission.";

    // Auto-generate slug if empty or create
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = generateSlug($name); // Using helper from functions.php
    }

    // Handle Image Logic
    $image_url = $_POST['existing_image_url'] ?? ''; // Default to keep existing

    if (empty($error)) {
        // 1. Check if new URL provided (Text Only)
        // If image_file is unset (or error), we might pick up the text input.
        if (!empty($_POST['image_url_input'])) {
            // If we are replacing an old local image with a URL, delete the old one
            if (!empty($id)) {
                $oldRec = $db->fetch("SELECT image_url FROM destinations WHERE id = ?", [$id]);
                if ($oldRec && $oldRec['image_url'] !== $_POST['image_url_input']) {
                    deleteOldImage($oldRec['image_url']);
                }
            }
            $image_url = trim($_POST['image_url_input']);
        }

        // 2. Check if file uploaded (File takes precedence over text URL if both present)
        // 2. Check if file uploaded
        if (isset($_FILES['image_file'])) {
            if ($_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/destinations/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                    // Delete old image if it exists
                    if (!empty($id)) {
                        $oldRec = $db->fetch("SELECT image_url FROM destinations WHERE id = ?", [$id]);
                        if ($oldRec) {
                            deleteOldImage($oldRec['image_url']);
                        }
                    }
                    $image_url = 'assets/images/destinations/' . $fileName;
                } else {
                    $error = "Failed to upload image file. Check directory permissions.";
                }
            } elseif ($_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
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

    $rating = $_POST['rating'] ?? 4.5;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $map_embed = $_POST['map_embed'] ?? ''; // Raw HTML
    $search_intent = $_POST['search_intent'] ?? 'Informational';

    if (empty($error)) {
        try {
            if ($action === 'update' && !empty($id)) {
                $db->execute(
                    "UPDATE destinations SET name=?, slug=?, country=?, description=?, type=?, image_url=?, rating=?, is_featured=?, is_new=?, map_embed=?, search_intent=? WHERE id=?",
                    [$name, $slug, $country, $description, $type, $image_url, $rating, $is_featured, $is_new, $map_embed, $search_intent, $id]
                );
                $message = "Destination updated successfully!";

                // Auto-Index
                require_once __DIR__ . '/../includes/classes/GoogleIndexer.php';
                $indexer = new GoogleIndexer();
                $fullUrl = 'https://ifytravels.com/destinations/' . $slug;
                $indexResult = $indexer->indexUrl($fullUrl, 'URL_UPDATED');
                if ($indexResult['status'] === 'success') {
                    $message .= " (Google notified)";
                }

            } else {
                $db->execute(
                    "INSERT INTO destinations (name, slug, country, description, type, image_url, rating, is_featured, map_embed, search_intent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$name, $slug, $country, $description, $type, $image_url, $rating, $is_featured, $map_embed, $search_intent]
                );
                $message = "Destination created successfully!";

                // Auto-Index
                require_once __DIR__ . '/../includes/classes/GoogleIndexer.php';
                $indexer = new GoogleIndexer();
                $fullUrl = 'https://ifytravels.com/destinations/' . $slug;
                $indexResult = $indexer->indexUrl($fullUrl, 'URL_UPDATED');
                if ($indexResult['status'] === 'success') {
                    $message .= " (Google notified)";
                }
            }
        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    // Get image to delete
    $rec = $db->fetch("SELECT image_url FROM destinations WHERE id = ?", [$idToDelete]);
    if ($rec) {
        deleteOldImage($rec['image_url']);
    }

    $db->execute("DELETE FROM destinations WHERE id = ?", [$idToDelete]);
    redirect('destinations.php');
}

// Fetch record for editing
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $db->query("SELECT * FROM destinations WHERE id = ?", [$_GET['edit']]);
    $editData = $stmt->fetch();
}

$destinations = $db->fetchAll("SELECT * FROM destinations ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Destinations - Admin</title>
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
                <?php echo $editData ? 'Edit Destination' : 'Manage Destinations'; ?>
            </h1>
            <button onclick="openModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md transition">
                + Add Destination
            </button>
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

        <!-- Modal Content -->
        <div id="formModal"
            class="fixed inset-y-0 right-0 w-full md:w-1/3 bg-white shadow-2xl z-50 transform transition-transform duration-300 translate-x-full overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        <?php echo $editData ? 'Edit Destination' : 'Add New Destination'; ?>
                    </h2>
                    <a href="destinations.php" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
                <form method="POST" enctype="multipart/form-data"
                    action="destinations.php<?php echo $editData ? '?edit=' . $editData['id'] : ''; ?>">
                    <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                    <?php endif; ?>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" required value="<?php echo e($editData['name'] ?? ''); ?>"
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Country</label>
                                <input type="text" name="country" required
                                    value="<?php echo e($editData['country'] ?? ''); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Type</label>
                                <select name="type"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="International" <?php echo ($editData['type'] ?? '') === 'International' ? 'selected' : ''; ?>>International</option>
                                    <option value="Domestic" <?php echo ($editData['type'] ?? '') === 'Domestic' ? 'selected' : ''; ?>>Domestic</option>
                                </select>
                            </div>
                        </div>

                        <!-- SEO Metadata -->
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Search Intent (Strategy)</label>
                            <select name="search_intent"
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg bg-white">
                                <option value="Informational" <?php echo ($editData && ($editData['search_intent'] ?? '') === 'Informational') ? 'selected' : ''; ?>>Informational (Guide - Default for
                                    Destinations)</option>
                                <option value="Commercial" <?php echo ($editData && ($editData['search_intent'] ?? '') === 'Commercial') ? 'selected' : ''; ?>>Commercial (Top 10 Lists)</option>
                                <option value="Navigational" <?php echo ($editData && ($editData['search_intent'] ?? '') === 'Navigational') ? 'selected' : ''; ?>>Navigational (Brand Focus)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Defines how this page contributes to the semantic
                                graph.</p>
                        </div>

                        <!-- Slug (Optional, auto-generated) -->
                        <?php if ($editData): ?>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Slug (URL)</label>
                                <input type="text" name="slug" value="<?php echo e($editData['slug'] ?? ''); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50">
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                            <div id="editor-container" class="bg-white" style="height: 200px;">
                                <?php echo $editData['description'] ?? ''; ?>
                            </div>
                            <input type="hidden" name="description"
                                value="<?php echo htmlspecialchars($editData['description'] ?? ''); ?>">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Rating (1-5)</label>
                                <input type="number" step="0.1" min="1" max="5" name="rating"
                                    value="<?php echo e($editData['rating'] ?? '4.5'); ?>"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg">
                            </div>
                            <div class="flex items-center mt-6">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" <?php echo ($editData && !empty($editData['is_featured'])) ? 'checked' : ''; ?>
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_featured" class="ml-2 block text-sm font-bold text-gray-700">Featured
                                    / Top Priority</label>
                            </div>
                        </div>

                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="is_new" id="is_new" value="1" <?php echo ($editData && !empty($editData['is_new'])) ? 'checked' : ''; ?>
                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="is_new" class="ml-2 block text-sm font-bold text-gray-700">Mark as NEW (Top
                                of List)</label>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Map Embed Code (Google Maps
                                Only)</label>
                            <textarea name="map_embed" rows="3" placeholder='<iframe src="...">'
                                class="w-full border border-gray-300 px-3 py-2 rounded-lg font-mono text-xs"><?php echo e($editData['map_embed'] ?? ''); ?></textarea>
                        </div>

                        <!-- Image Management -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Destination Image</label>

                            <!-- Current Image Preview -->
                            <?php if (!empty($editData['image_url'])): ?>
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Current Image:</p>
                                    <img src="<?php echo base_url($editData['image_url']); ?>" alt="Current"
                                        class="h-24 w-full object-cover rounded shadow-sm">
                                    <input type="hidden" name="existing_image_url"
                                        value="<?php echo e($editData['image_url']); ?>">
                                </div>
                            <?php endif; ?>

                            <!-- Option 1: URL -->
                            <div class="mb-3">
                                <label class="text-xs font-semibold text-gray-600 block mb-1">Option A: Image URL
                                    (Link)</label>
                                <input type="text" name="image_url_input" placeholder="https://example.com/image.jpg"
                                    class="w-full border border-gray-300 px-3 py-2 rounded text-sm placeholder-gray-400">
                            </div>

                            <!-- Option 2: Upload -->
                            <div>
                                <label class="text-xs font-semibold text-gray-600 block mb-1">Option B: Upload File
                                    (Overrides URL)</label>
                                <input type="file" name="image_file" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-bold hover:bg-blue-700 shadow-md transition transform hover:-translate-y-0.5">
                            <?php echo $editData ? 'Save Changes' : 'Create Destination'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- List Section -->
        <div class="w-full">

            <!-- List Section -->

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b">
                            <tr>
                                <th class="px-6 py-4">Image</th>
                                <th class="px-6 py-4">Name / Country</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($destinations as $d): ?>
                                <tr
                                    class="hover:bg-blue-50 transition <?php echo ($editData && $editData['id'] == $d['id']) ? 'bg-blue-50 ring-2 ring-inset ring-blue-100' : ''; ?>">
                                    <td class="px-6 py-4">
                                        <div class="w-16 h-12 rounded-lg bg-gray-200 bg-cover bg-center shadow-sm"
                                            style="background-image: url('<?php echo base_url($d['image_url'] ?? ''); ?>')">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-base"><?php echo e($d['name']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500"><?php echo e($d['country']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">
                                            <?php echo e($d['type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="?edit=<?php echo $d['id']; ?>"
                                            class="inline-block text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1 rounded-full text-xs font-bold transition">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $d['id']; ?>"
                                            class="inline-block text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-full text-xs font-bold transition"
                                            onclick="return confirm('Are you sure you want to delete this destination? Packages linked to it might break.')">
                                            <i class="fas fa-trash-alt mr-1"></i> Delete
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
            placeholder: 'Describe this destination...',
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

        // Modal Logic
        const modalOverlay = document.getElementById('modalOverlay');
        constformModal = document.getElementById('formModal');
        const body = document.body;

        function openModal() {
            modalOverlay.classList.remove('hidden');
            formModal.classList.remove('translate-x-full');
            body.classList.add('overflow-hidden');

            // If opening empty modal (not via edit param), clear form if needed or just show as is (since it reloads page for clean state mostly)
            // But if we are in 'edit' mode (PHP), we are already open.
        }

        function closeModal() {
            window.location.href = 'destinations.php'; // Easiest way to reset state
        }

        // Auto-open if Editing or Error
        <?php if ($editData || $error): ?>
            openModal();
        <?php endif; ?>
    </script>
</body>

</html>