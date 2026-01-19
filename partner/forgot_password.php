<?php
require_once __DIR__ . '/../includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $db = Database::getInstance();
    $aff = $db->fetch("SELECT id, name FROM affiliates WHERE email = ?", [$email]);

    if ($aff) {
        // Generate Token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $db->execute("UPDATE affiliates SET reset_token = ?, reset_expiry = ? WHERE id = ?", [$token, $expiry, $aff['id']]);

        if (send_password_reset_email($email, $token)) {
            $success = "A reset link has been sent to your email.";
        } else {
            $error = "Failed to send email. Please try again.";
        }
    } else {
        // Pseudo-success to prevent email scraping
        $success = "If that email exists, a reset link has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot Password - ifyTravels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-gray-500 text-sm mt-2">Enter your registered email to receive a reset link.</p>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-700 p-4 rounded-lg text-center mb-6 border border-green-100">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg text-center mb-6 border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-600 outline-none">
            </div>
            <button type="submit"
                class="w-full bg-teal-600 text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition">Send Reset
                Link</button>
        </form>

        <div class="text-center mt-6">
            <a href="login.php" class="text-sm text-gray-400 hover:text-gray-600">Back to Login</a>
        </div>
    </div>
</body>

</html>