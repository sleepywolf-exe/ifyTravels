<?php
// admin/packages.php
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

    $features_json = json_encode(array_values($features));
    $inclusions_json = json_encode(array_values($inclusions));
    $exclusions_json = json_encode(array_values($exclusions));

    // Auto-generate slug
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = generateSlug($title);
    }

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
                    "UPDATE packages SET title=?, slug=?, destination_id=?, price=?, duration=?, description=?, image_url=?, is_popular=?, features=?, inclusions=?, exclusions=? WHERE id=?",
                    [$title, $slug, $destination_id, $price, $duration, $description, $image_url, $is_popular, $features_json, $inclusions_json, $exclusions_json, $id]
                );
                $message = "Package updated successfully!";
            } else {
                $db->execute(
                    "INSERT INTO packages (title, slug, destination_id, price, duration, description, image_url, is_popular, features, inclusions, exclusions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$title, $slug, $destination_id, $price, $duration, $description, $image_url, $is_popular, $features_json, $inclusions_json, $exclusions_json]
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Summernote CSS/JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Override Summernote font to match theme */
        .note-editor .note-editing-area {
            font-family: 'Outfit', sans-serif !important;
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
                <a href="packages.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-600">
                    &larr; Back to List
                </a>
            <?php endif; ?>
        </header>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6"><?php echo e($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold mb-4 border-b pb-2">
                        <?php echo $editData ? 'Update Package' : 'Add New Package'; ?>
                    </h2>
                    <form method="POST" enctype="multipart/form-data"
                        action="packages.php<?php echo $editData ? '?edit=' . $editData['id'] : ''; ?>">
                        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                        <?php if ($editData): ?>
                            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                        <?php endif; ?>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" required
                                    value="<?php echo e($editData['title'] ?? ''); ?>"
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
                                <input type="checkbox" name="is_popular" id="is_popular" value="1" <?php echo ($editData && $editData['is_popular']) ? 'checked' : ''; ?>
                                    class="w-4 h-4 text-blue-600 rounded">
                                <label for="is_popular" class="text-sm font-bold text-gray-700">Mark as Popular?</label>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                                <textarea id="summernote" name="description" rows="3"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg"><?php echo e($editData['description'] ?? ''); ?></textarea>
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

            <!-- List -->
            <div class="lg:col-span-2">
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
        </div>
    </main>
    <script>
        $('#summernote').summernote({
            placeholder: 'Write a beautiful description...',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
</body>

</html>