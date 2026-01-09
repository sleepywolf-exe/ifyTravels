<?php
// admin/change_password.php
require 'auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Verify current password
    // We need to fetch the current user's password hash
    $adminId = $_SESSION['admin_id'];
    $user = $db->fetch("SELECT password_hash FROM users WHERE id = ?", [$adminId]);

    if ($user && password_verify($current, $user['password_hash'])) {
        if (strlen($new) < 6) {
            $error = "New password must be at least 6 characters.";
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match.";
        } else {
            // Update
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $db->execute("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $adminId]);
            $message = "Password updated successfully.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password - Admin</title>
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
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Change Password</h1>
        </header>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 max-w-lg">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 max-w-lg">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-lg">
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition w-full">
                    Update Password
                </button>
            </form>
        </div>
    </main>
</body>

</html>