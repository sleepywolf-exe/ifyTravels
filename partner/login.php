<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../data/loader.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['partner_logged_in']) && $_SESSION['partner_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $db = Database::getInstance();
        $affiliate = $db->fetch("SELECT * FROM affiliates WHERE email = ? AND status = 'active'", [$email]);

        if ($affiliate && password_verify($password, $affiliate['password_hash'])) {
            $_SESSION['partner_logged_in'] = true;
            $_SESSION['partner_id'] = $affiliate['id'];
            $_SESSION['partner_name'] = $affiliate['name'];

            // Update last login
            $db->execute("UPDATE affiliates SET last_login = ? WHERE id = ?", [date('Y-m-d H:i:s'), $affiliate['id']]);

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login -
        <?php echo get_setting('site_name', 'ifyTravels'); ?>
    </title>
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

<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0 bg-[url('../assets/images/travel-doodles.png')] opacity-10 bg-repeat bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 via-gray-900/80 to-secondary/20"></div>

    <div class="relative z-10 w-full max-w-md px-6">
        <div class="glass-form">
            <div class="text-center mb-8">
                <img src="<?php echo base_url('assets/images/logo-white.png'); ?>" alt="Logo" class="h-12 mx-auto mb-6">
                <h1 class="text-3xl font-bold font-heading mb-2">Partner Portal</h1>
                <p class="text-gray-400">Welcome back! Please login to your dashboard.</p>
            </div>

            <?php if ($error): ?>
                <div
                    class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-5">
                    <label class="glass-label">Email Address</label>
                    <input type="email" name="email" required class="glass-input w-full" placeholder="you@example.com">
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="glass-label mb-0">Password</label>
                    </div>
                    <input type="password" name="password" required class="glass-input w-full" placeholder="••••••••">
                </div>

                <button type="submit" class="glass-button w-full justify-center shadow-xl">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-400">
                Not a partner yet? <a href="<?php echo base_url('pages/partner-program.php'); ?>"
                    class="text-secondary hover:text-white transition">Join the program</a>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="<?php echo base_url(''); ?>"
                class="text-gray-500 hover:text-white transition text-sm flex items-center justify-center gap-2">
                &larr; Back to Website
            </a>
        </div>
    </div>

</body>

</html>