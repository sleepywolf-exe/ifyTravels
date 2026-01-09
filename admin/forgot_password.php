<?php
// admin/forgot_password.php
require '../includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email)) {
        $db = Database::getInstance();

        // 1. Check if user exists
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user) {
            // 2. Generate Token
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 3600; // 1 hour

            // 3. Store Token
            $db->execute("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)", [$email, $token, $expiry]);

            // 4. Send Email
            $resetLink = base_url("admin/reset_password.php?token=$token&email=" . urlencode($email));

            // Attempt to send email
            $subject = "Password Reset Request";
            $emailMessage = "Hello,\n\nPlease click the following link to reset your administrator password:\n$resetLink\n\nIf you did not request this, please ignore this email.\n\nRegards,\nifyTravels Admin";
            $headers = "From: no-reply@ifytravels.com";

            // Try sending email (might fail on localhost without SMTP)
            @mail($email, $subject, $emailMessage, $headers);

            error_log("Password Reset Link for $email: $resetLink");

            $message = "If an account exists for this email, a reset link has been sent.";

            // Show link on localhost or if explicitly requested for testing
            if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                $debugLink = $resetLink;
            }
        } else {
            // Security: Don't reveal if user exists or not, but for admin panel usually fine.
            // Let's use generic message.
            $message = "If an account exists for this email, a reset link has been sent.";
        }
    } else {
        $error = "Please enter your email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Admin</title>
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
            <h2 class="text-3xl font-bold text-white mb-2">Recovery</h2>
            <p class="text-white/80">Enter your email to reset password</p>
        </div>

        <?php if ($message): ?>
            <div
                class="bg-green-500/20 text-white p-3 rounded-lg border border-green-500/30 text-center mb-6 backdrop-blur-sm">
                <?php echo e($message); ?>
            </div>
            <?php if (isset($debugLink)): ?>
                <div class="bg-white/90 p-4 rounded text-black text-xs mb-4 break-all">
                    <strong>Dev Mode Link:</strong><br>
                    <a href="<?php echo $debugLink; ?>" class="text-blue-600 underline"><?php echo $debugLink; ?></a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-500/20 text-white p-3 rounded-lg border border-red-500/30 text-center mb-6 backdrop-blur-sm">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="glass-label text-white">Email Address</label>
                <input type="email" name="email" required placeholder="admin@example.com"
                    class="glass-input w-full placeholder-white/50 text-white">
            </div>

            <button type="submit" class="glass-button w-full">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="login.php" class="text-white/70 hover:text-white transition">Back to Login</a>
        </div>
    </div>
</body>

</html>