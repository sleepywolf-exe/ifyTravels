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
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $rating = $_POST['rating'] ?? 5;
        $msg = $_POST['message'] ?? '';
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include 'sidebar_inc.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10 p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                <?php echo $editTestimonial ? 'Edit Testimonial' : 'Testimonials'; ?>
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, Admin</span>
                <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <?php if ($message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form Section -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">
                            <?php echo $editTestimonial ? 'Update' : 'Add New'; ?> Testimonial
                        </h3>
                        <form method="POST" action="testimonials.php">
                            <?php if ($editTestimonial): ?>
                                <input type="hidden" name="id" value="<?php echo $editTestimonial['id']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                <input type="text" name="name" value="<?php echo $editTestimonial['name'] ?? ''; ?>"
                                    required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Location/Title</label>
                                <input type="text" name="location"
                                    value="<?php echo $editTestimonial['location'] ?? ''; ?>"
                                    placeholder="e.g. Paris, France"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Rating (1-5)</label>
                                <input type="number" name="rating" min="1" max="5"
                                    value="<?php echo $editTestimonial['rating'] ?? '5'; ?>" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                                <textarea name="message" rows="4" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo $editTestimonial['message'] ?? ''; ?></textarea>
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    <?php echo $editTestimonial ? 'Update' : 'Add'; ?>
                                </button>
                                <?php if ($editTestimonial): ?>
                                    <a href="testimonials.php" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List Section -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-gray-700 font-bold uppercase text-sm">Existing Testimonials</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                <?php echo count($testimonials); ?> Total
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Message</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rating</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($testimonials as $t): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($t['name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($t['location']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-xs truncate"
                                                    title="<?php echo htmlspecialchars($t['message']); ?>">
                                                    <?php echo htmlspecialchars($t['message']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-yellow-500">
                                                    <?php echo str_repeat('★', $t['rating']); ?>
                                                    <span class="text-gray-300">
                                                        <?php echo str_repeat('★', 5 - $t['rating']); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="?edit=<?php echo $t['id']; ?>"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                <form method="POST" action="testimonials.php" class="inline-block"
                                                    onsubmit="return confirm('Are you sure?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($testimonials)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No testimonials
                                                found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>