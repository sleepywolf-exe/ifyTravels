<?php
// admin/testimonials.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
include 'auth_check.php';

$pdo = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Testimonial deleted successfully.";
        } else {
            $error = "Failed to delete testimonial.";
        }
    } else {
        $name = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $rating = intval($_POST['rating'] ?? 5);
        $msg = trim($_POST['message'] ?? '');
        $id = $_POST['id'] ?? '';

        if (!empty($name) && !empty($msg)) {
            if (!empty($id)) {
                // Update
                $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, location = ?, rating = ?, message = ? WHERE id = ?");
                if ($stmt->execute([$name, $location, $rating, $msg, $id])) {
                    $message = "Testimonial updated successfully.";
                } else {
                    $error = "Failed to update testimonial.";
                }
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO testimonials (name, location, rating, message) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $location, $rating, $msg])) {
                    $message = "Testimonial added successfully.";
                } else {
                    $error = "Failed to add testimonial.";
                }
            }
        } else {
            $error = "Name and Message are required.";
        }
    }
}

// Fetch All Testimonials
$testimonials = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}

$editTestimonial = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editTestimonial = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include 'sidebar_inc.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 z-10 sticky top-0">
            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-800 to-gray-600">
                <?php echo $editTestimonial ? 'Edit Testimonial' : 'Manage Testimonials'; ?>
            </h1>
            <div class="flex items-center gap-4">
                <a href="testimonials.php" class="text-gray-500 hover:text-blue-600 transition text-sm font-medium">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 lg:p-10">
            <?php if ($message): ?>
                <div
                    class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i
                            class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div
                    class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i
                            class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Form Section (Sticky on large screens) -->
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                                <i class="fas fa-<?php echo $editTestimonial ? 'edit' : 'plus'; ?>"></i>
                            </span>
                            <?php echo $editTestimonial ? 'Edit Review' : 'Add Review'; ?>
                        </h3>

                        <form method="POST" action="testimonials.php" class="space-y-4">
                            <?php if ($editTestimonial): ?>
                                <input type="hidden" name="id" value="<?php echo $editTestimonial['id']; ?>">
                            <?php endif; ?>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Users
                                    Name</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-user-circle"></i>
                                    </span>
                                    <input type="text" name="name"
                                        value="<?php echo e($editTestimonial['name'] ?? ''); ?>" required
                                        class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-700 font-medium"
                                        placeholder="e.g. John Doe">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Location</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="location"
                                        value="<?php echo e($editTestimonial['location'] ?? ''); ?>"
                                        class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-700"
                                        placeholder="e.g. London, UK">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rating</label>
                                <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <?php $currentRating = $editTestimonial['rating'] ?? 5; ?>
                                    <div class="flex-1">
                                        <input type="range" name="rating" min="1" max="5"
                                            value="<?php echo $currentRating; ?>" class="w-full accent-blue-600"
                                            oninput="document.getElementById('rating-val').innerText = this.value + ' Stars'">
                                    </div>
                                    <span id="rating-val"
                                        class="text-sm font-bold text-blue-600"><?php echo $currentRating; ?>
                                        Stars</span>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Review
                                    Message</label>
                                <textarea name="message" rows="5" required
                                    class="w-full p-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-700 leading-relaxed resize-none"
                                    placeholder="What did the customer say?"><?php echo e($editTestimonial['message'] ?? ''); ?></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-200 transition transform active:scale-95 flex items-center justify-center">
                                    <i class="fas fa-save mr-2"></i>
                                    <?php echo $editTestimonial ? 'Update Review' : 'Publish Review'; ?>
                                </button>
                                <?php if ($editTestimonial): ?>
                                    <a href="testimonials.php"
                                        class="block text-center mt-3 text-gray-400 hover:text-gray-600 text-xs font-bold uppercase tracking-wide">Cancel
                                        Editing</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List Section (Masonry Grid) -->
                <div class="lg:col-span-8 xl:col-span-9">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                        <?php if (empty($testimonials)): ?>
                            <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                                <div class="bg-gray-100 rounded-full p-6 mb-4">
                                    <i class="fas fa-comment-slash text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-600">No Testimonials Yet</h3>
                                <p class="text-gray-400 max-w-sm mt-2">Add your first customer review using the form
                                    properly.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($testimonials as $t): ?>
                                <div
                                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
                                    <!-- Quote Icon Background -->
                                    <div
                                        class="absolute top-4 right-6 text-6xl text-gray-50 font-serif opacity-50 select-none group-hover:text-blue-50 transition">
                                        ”</div>

                                    <!-- Header -->
                                    <div class="flex items-start justify-between relative z-10 mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                                <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 leading-tight"><?php echo e($t['name']); ?>
                                                </h4>
                                                <p class="text-xs text-gray-400">
                                                    <?php echo e($t['location'] ?: 'Unknown Location'); ?></p>
                                            </div>
                                        </div>
                                        <!-- Actions Dropdown (Hover) -->
                                        <div
                                            class="flex gap-1 opacity-0 group-hover:opacity-100 transition duration-200 bg-white/90 backdrop-blur rounded-lg p-1 shadow-sm border border-gray-100">
                                            <a href="?edit=<?php echo $t['id']; ?>"
                                                class="p-2 text-blue-500 hover:bg-blue-50 rounded-md transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="testimonials.php" class="inline"
                                                onsubmit="return confirm('Delete this review?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                <button type="submit"
                                                    class="p-2 text-red-500 hover:bg-red-50 rounded-md transition"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center gap-1 mb-3 text-sm">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i
                                                class="fas fa-star <?php echo $i <= $t['rating'] ? 'text-yellow-400' : 'text-gray-200'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-xs font-medium text-gray-400 uppercase tracking-wide">
                                            <?php echo $t['rating']; ?>.0 / 5.0
                                        </span>
                                    </div>

                                    <!-- Message -->
                                    <p class="text-gray-600 text-sm leading-relaxed relative z-10 italic">
                                        "<?php echo e($t['message']); ?>"
                                    </p>

                                    <!-- Date Footer -->
                                    <div
                                        class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-xs text-gray-400">
                                        <span><i class="far fa-calendar-alt mr-1"></i>
                                            <?php echo date('M d, Y', strtotime($t['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>