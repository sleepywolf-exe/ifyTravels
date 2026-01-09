<?php
// admin/reset_password.php
require '../includes/functions.php';

$message = '';
$error = '';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($token) || empty($email)) {
    die("Invalid request.");
}

$db = Database::getInstance();

// Validate Token
$resetRequest = $db->fetch(
    "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expiry > ?", 
    [$email, $token, time()]
);

if (!$resetRequest && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error = "Invalid or expired link.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$resetRequest) {
        $error = "Invalid or expired link.";
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // Update User Password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->execute("UPDATE users SET password_hash = ? WHERE email = ?", [$hash, $email]);

            // Delete Token
            $db->execute("DELETE FROM password_resets WHERE email = ?", [$email]);

            $message = "Password has been reset! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/glassmorphism.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .admin-bg {
            background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="admin-bg flex items-center justify-center min-h-screen relative">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>

    <div class="glass-form max-w-md w-full m-4 p-8 relative z-10">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white mb-2">Reset Password</h2>
            <?php if ($resetRequest): ?>
                <p class="text-white/80">Set a new secure password</p>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-500/20 text-white p-3 rounded-lg border border-green-500/30 text-center mb-6 backdrop-blur-sm">
                <?php echo e($message); ?>
            </div>
            <div class="mt-6 text-center">
                <a href="login.php" class="glass-button w-full inline-block">Go to Login</a>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-red-500/20 text-white p-3 rounded-lg border border-red-500/30 text-center mb-6 backdrop-blur-sm">
                <?php echo e($error); ?>
            </div>
             <div class="mt-6 text-center text-sm">
                <a href="forgot_password.php" class="text-white/70 hover:text-white transition">Request new link</a>
            </div>
        <?php else: ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="glass-label text-white">New Password</label>
                    <input type="password" name="password" required class="glass-input w-full placeholder-white/50 text-white" placeholder="Min 6 chars">
                </div>
                <div>
                    <label class="glass-label text-white">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="glass-input w-full placeholder-white/50 text-white" placeholder="Confirm">
                </div>

                <button type="submit" class="glass-button w-full">
                    Reset Password
                </button>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
