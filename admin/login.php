<?php
// admin/login.php - Secure admin authentication
require '../includes/functions.php';

// Redirect if already logged in
if (is_admin()) {
    redirect('dashboard.php');
}

// Regenerate session ID for security
session_regenerate_id(true);

$error = '';
$loginAttempts = $_SESSION['login_attempts'] ?? 0;
$lastAttempt = $_SESSION['last_attempt'] ?? 0;

// Rate limiting: max 5 attempts per 15 minutes
if ($loginAttempts >= 5 && (time() - $lastAttempt) < 900) {
    $error = "Too many login attempts. Please try again in 15 minutes.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $db = Database::getInstance();

        $user = $db->fetch("SELECT * FROM users WHERE email = ? AND role = 'admin'", [$email]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Reset attempts on success
            unset($_SESSION['login_attempts'], $_SESSION['last_attempt']);

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            redirect('dashboard.php');
        } else {
            $_SESSION['login_attempts'] = $loginAttempts + 1;
            $_SESSION['last_attempt'] = time();
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo e(get_setting('site_name', 'ifyTravels')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/glassmorphism.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Override body background if glassmorphism needs a darker/image bg for effect, 
           but the user asked for form redesign, not necessarily whole page bg change, 
           though glass looks best on colorful bgs. 
           I'll add the hero video background or a nice gradient to make the glass effect visible. 
           For admin, a subtle animated gradient might be professional. */
        .admin-bg {
            background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
            background-size: cover;
            background-position: center;
        }

        .admin-overlay {
            background: linear-gradient(135deg, rgba(66, 153, 225, 0.8), rgba(129, 230, 217, 0.8));
        }
    </style>
</head>

<body class="admin-bg flex items-center justify-center min-h-screen relative">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>

    <div class="glass-form max-w-md w-full m-4 p-8 relative z-10">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white mb-2 shadow-sm">Admin Panel</h2>
            <p class="text-white/80">Secure Access</p>
        </div>

        <form method="POST" class="space-y-6">
            <div>
                <label class="glass-label text-white">Email Address</label>
                <input type="email" name="email" required placeholder="admin@ifytravels.com"
                    value="<?php echo e($_POST['email'] ?? ''); ?>"
                    class="glass-input w-full placeholder-white/50 text-white">
            </div>

            <div>
                <label class="glass-label text-white">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="glass-input w-full placeholder-white/50 text-white">
                <div class="text-right mt-1">
                    <a href="forgot_password.php" class="text-xs text-white/80 hover:text-white transition">Forgot
                        Password?</a>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/20 text-white p-3 rounded-lg border border-red-500/30 text-center backdrop-blur-sm">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="glass-button w-full">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="../index.php" class="text-white/70 hover:text-white transition">Back to Website</a>
        </div>
    </div>

</body>

</html>