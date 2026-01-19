<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../data/loader.php';

$db = Database::getInstance();
$partnerId = $_SESSION['partner_id'];
$message = '';
$error = '';

// Fetch Partner Details
$partner = $db->fetch("SELECT * FROM affiliates WHERE id = ?", [$partnerId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        // Update basic info
        $updateSql = "UPDATE affiliates SET name = ?, email = ? WHERE id = ?";
        $params = [$name, $email, $partnerId];

        // Update password if provided
        if (!empty($password)) {
            if (strlen($password) < 8) {
                $error = "Password must be at least 8 characters.";
            } else {
                $updateSql = "UPDATE affiliates SET name = ?, email = ?, password_hash = ? WHERE id = ?";
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $params = [$name, $email, $hash, $partnerId];
            }
        }

        if (empty($error)) {
            try {
                if ($db->execute($updateSql, $params)) {
                    $message = "Profile updated successfully.";
                    // Refresh data
                    $partner = $db->fetch("SELECT * FROM affiliates WHERE id = ?", [$partnerId]);
                    $_SESSION['partner_name'] = $partner['name'];
                } else {
                    $error = "Failed to update profile. Email might be in use.";
                }
            } catch (Exception $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Partner Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glassmorphism.css'); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                    },
                    fontFamily: { heading: ['Poppins', 'sans-serif'], body: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-white min-h-screen relative overflow-x-hidden">

    <!-- Background -->
    <div
        class="fixed inset-0 bg-[url('../assets/images/travel-doodles.png')] opacity-5 bg-repeat bg-center pointer-events-none">
    </div>
    <div class="fixed inset-0 bg-gradient-to-tr from-gray-900/95 via-gray-900/90 to-primary/10 pointer-events-none">
    </div>

    <!-- Layout -->
    <div class="relative z-10 flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 glass-form !rounded-none !border-l-0 !border-y-0 fixed h-full hidden md:flex flex-col">
            <div class="mb-10 px-4">
                <img src="<?php echo base_url('assets/images/logo-white.png'); ?>" alt="Logo" class="h-10">
                <p class="text-xs text-gray-500 mt-2 ml-1">PARTNER PORTAL</p>
            </div>

            <nav class="flex-1 space-y-2 px-2">
                <a href="dashboard.php"
                    class="flex items-center space-x-3 px-4 py-3 hover:bg-white/5 rounded-xl text-gray-300 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="profile.php"
                    class="flex items-center space-x-3 px-4 py-3 bg-white/10 rounded-xl text-white font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>My Profile</span>
                </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <a href="logout.php" class="block text-center py-2 text-sm text-red-400 hover:text-red-300">Sign Out</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-4 md:p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold font-heading">My Profile</h1>
                    <p class="text-gray-400 text-sm">Manage your account details</p>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="bg-green-500/20 border border-green-500/50 text-green-200 px-6 py-4 rounded-xl mb-6">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-6 py-4 rounded-xl mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="glass-card p-8 rounded-2xl max-w-2xl">
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="glass-label">Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($partner['name']); ?>"
                                required class="glass-input w-full">
                        </div>
                        <div>
                            <label class="glass-label">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($partner['email']); ?>"
                                required class="glass-input w-full">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="glass-label">Referral Code (Fixed)</label>
                        <input type="text" value="<?php echo htmlspecialchars($partner['code']); ?>" disabled
                            class="glass-input w-full !bg-white/5 !text-gray-500">
                    </div>

                    <div class="border-t border-white/10 my-6"></div>

                    <div class="mb-6">
                        <label class="glass-label">Change Password (Optional)</label>
                        <input type="password" name="password" class="glass-input w-full"
                            placeholder="Leave blank to keep current password">
                    </div>

                    <button type="submit" class="glass-button w-full md:w-auto">
                        Save Changes
                    </button>
                </form>
            </div>

        </main>
    </div>

</body>

</html>