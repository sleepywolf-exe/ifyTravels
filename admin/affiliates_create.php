<?php
require 'auth_check.php';

$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $code = strtoupper(trim($_POST['code']));

    if (empty($name) || empty($email) || empty($code)) {
        $error = "All fields are required.";
    } else {
        $exists = $db->fetch("SELECT id FROM affiliates WHERE code = ? OR email = ?", [$code, $email]);
        if ($exists) {
            $error = "Affiliate code or email already exists.";
        } else {
            if ($db->execute("INSERT INTO affiliates (name, email, code, status) VALUES (?, ?, ?, 'active')", [$name, $email, $code])) {
                redirect('affiliates.php?success=created');
            } else {
                $error = "Database Error: Failed to create affiliate.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Affiliate - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
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
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-4 mb-6">
                <a href="affiliates.php" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Add New Affiliate</h1>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100 text-sm">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Partner Name</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="e.g. John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="partner@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Affiliate Code</label>
                        <input type="text" name="code" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition uppercase"
                            placeholder="e.g. SUMMER25">
                        <p class="text-xs text-gray-500 mt-1">Must be unique. Will be converted to uppercase.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition shadow-lg">
                            Create Partner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>