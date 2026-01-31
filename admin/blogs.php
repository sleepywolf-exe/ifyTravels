<?php
// admin/blogs.php
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
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Post deleted successfully.";
        } else {
            $error = "Failed to delete post.";
        }
    } else {
        $title = trim($_POST['title'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author = trim($_POST['author'] ?? ''); // Added author input capture
        $id = $_POST['id'] ?? '';

        // Generate Slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        // File Upload Handling
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/blog/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $fileExt = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExt, $allowed)) {
                $fileName = time() . '_' . uniqid() . '.' . $fileExt;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
                    $image_url = 'assets/uploads/blog/' . $fileName;
                } else {
                    $error = "Failed to move uploaded file.";
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, WEBP, GIF allowed.";
            }
        }

        if (!empty($title) && !empty($content) && empty($error)) {
            // Default author if empty
            if (empty($author))
                $author = 'Admin';

            if (!empty($id)) {
                // Update
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, image_url = ?, excerpt = ?, content = ?, author = ? WHERE id = ?");
                if ($stmt->execute([$title, $slug, $image_url, $excerpt, $content, $author, $id])) {
                    $message = "Post updated successfully.";
                } else {
                    $error = "Failed to update post.";
                }
            } else {
                // Insert
                // Check if slug exists
                $check = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
                $check->execute([$slug]);
                if ($check->fetchColumn() > 0) {
                    $slug .= '-' . time(); // Append timestamp to make unique
                }

                $stmt = $pdo->prepare("INSERT INTO posts (title, slug, image_url, excerpt, content, author) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$title, $slug, $image_url, $excerpt, $content, $author])) {
                    $message = "Post published successfully.";
                } else {
                    $error = "Failed to publish post.";
                }
            }
        } else if (empty($error)) {
            $error = "Title and Content are required.";
        }
    }
}

// Fetch All Posts
$posts = [];
try {
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}

$editPost = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editPost = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Admin</title>
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
                <?php echo $editPost ? 'Edit Blog Post' : 'Manage Blog Posts'; ?>
            </h1>
            <div class="flex items-center gap-4">
                <a href="<?php echo base_url('pages/blogs.php'); ?>" target="_blank"
                    class="text-blue-600 hover:text-blue-800 transition text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-external-link-alt"></i> View Blog Page
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 lg:p-10">
            <?php if ($message): ?>
                <div
                    class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center"><i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i
                            class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div
                    class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i
                            class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                                <i class="fas fa-<?php echo $editPost ? 'edit' : 'pen-fancy'; ?>"></i>
                            </span>
                            <?php echo $editPost ? 'Edit Post' : 'Write New Story'; ?>
                        </h3>

                        <form method="POST" action="blogs.php" enctype="multipart/form-data" class="space-y-4">
                            <?php if ($editPost): ?>
                                <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
                            <?php endif; ?>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Title</label>
                                <input type="text" name="title" value="<?php echo e($editPost['title'] ?? ''); ?>"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm font-bold text-gray-800 placeholder-gray-300"
                                    placeholder="Enter an engaging title...">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Author
                                    Name</label>
                                <input type="text" name="author"
                                    value="<?php echo e($editPost['author'] ?? 'Admin'); ?>"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm font-bold text-gray-800 placeholder-gray-300"
                                    placeholder="Who wrote this?">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cover
                                    Image</label>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <div
                                        class="relative border-2 border-dashed border-gray-200 bg-gray-50 rounded-xl p-4 text-center hover:bg-blue-50 hover:border-blue-200 transition cursor-pointer group">
                                        <input type="file" name="image_file" id="image_file"
                                            accept=".jpg,.jpeg,.png,.webp"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <div class="text-gray-400 group-hover:text-blue-500">
                                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                                            <p class="text-xs font-bold">Click to Upload Image</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- URL Input (Fallback) -->
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i
                                            class="fas fa-link"></i></span>
                                    <input type="text" name="image_url"
                                        value="<?php echo e($editPost['image_url'] ?? ''); ?>"
                                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-600 placeholder-gray-300"
                                        placeholder="Or paste an image URL...">
                                </div>
                            </div>


                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Short
                                    Excerpt</label>
                                <textarea name="excerpt" rows="3"
                                    class="w-full p-4 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-600 leading-relaxed resize-none"
                                    placeholder="Brief summary for the card view..."><?php echo e($editPost['excerpt'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Content</label>
                                <textarea name="content" rows="12" required
                                    class="w-full p-4 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm text-gray-800 leading-relaxed font-mono"
                                    placeholder="Write your story here... (HTML supported)"><?php echo e($editPost['content'] ?? ''); ?></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-blue-200 transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo $editPost ? 'Update Post' : 'Publish Story'; ?>
                                </button>
                                <?php if ($editPost): ?>
                                    <a href="blogs.php"
                                        class="block text-center mt-3 text-sm text-gray-400 hover:text-gray-600 font-medium">Cancel
                                        Edit</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List Section -->
                <div class="lg:col-span-7 xl:col-span-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php if (empty($posts)): ?>
                            <div
                                class="col-span-2 text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-feather-alt text-2xl"></i>
                                </div>
                                <h3 class="text-gray-500 font-medium">No stories yet. Write your first one!</h3>
                            </div>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <div
                                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all duration-300 flex flex-col h-full">
                                    <div class="h-48 overflow-hidden relative bg-gray-100">
                                        <?php if (!empty($post['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Cover"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        <?php else: ?>
                                            <div class="flex items-center justify-center h-full text-gray-300">
                                                <i class="fas fa-image text-3xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-3 right-3 flex gap-2">
                                            <a href="blogs.php?edit=<?php echo $post['id']; ?>"
                                                class="w-8 h-8 rounded-full bg-white/90 backdrop-blur text-blue-600 flex items-center justify-center shadow-sm hover:bg-blue-600 hover:text-white transition cursor-pointer">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </a>
                                            <form method="POST" action="blogs.php"
                                                onsubmit="return confirm('Are you sure? This cannot be undone.');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-white/90 backdrop-blur text-red-500 flex items-center justify-center shadow-sm hover:bg-red-500 hover:text-white transition cursor-pointer">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="p-5 flex-1 flex flex-col">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                        </div>
                                        <h3
                                            class="font-bold text-lg text-gray-800 mb-2 leading-tight group-hover:text-blue-600 transition-colors">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-1">
                                            <?php echo htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 100)); ?>
                                        </p>
                                        <div
                                            class="pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400 font-medium">
                                            <span><i class="far fa-eye mr-1"></i>
                                                <?php echo $post['views']; ?> Views
                                            </span>
                                            <span class="bg-gray-100 px-2 py-1 rounded">
                                                <?php echo htmlspecialchars($post['slug']); ?>
                                            </span>
                                        </div>
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