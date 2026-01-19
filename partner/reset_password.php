<?php
require_once __DIR__ . '/../includes/functions.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$validToken = false;

$db = Database::getInstance();

if ($token) {
    // Validate Token
    $aff = $db->fetch("SELECT id FROM affiliates WHERE reset_token = ? AND reset_expiry > NOW()", [$token]);
    if ($aff) {
        $validToken = true;
    } else {
        $error = "Invalid or expired reset token.";
    }
} else {
    $error = "No token provided.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($pass) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        // Update password and clear token
        $db->execute("UPDATE affiliates SET password_hash = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?", [$hash, $aff['id']]);
        $success = "Password successfully reset! You can now login.";
        $validToken = false; // Hide form
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Set New Password - ifyTravels</title>
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
            <h1 class="text-2xl font-bold text-gray-800">Set New Password</h1>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-700 p-4 rounded-lg text-center mb-6 border border-green-100">
                <?php echo $success; ?>
                <a href="login.php" class="block mt-4 font-bold underline">Go to Login</a>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg text-center mb-6 border border-red-100">
                <?php echo $error; ?>
                <?php if (!$validToken && $token): ?>
                    <a href="forgot_password.php" class="block mt-2 underline">Request new link</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($validToken): ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-600 outline-none"
                        placeholder="Min 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-600 outline-none"
                        placeholder="Repeat password">
                </div>
                <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition">Reset
                    Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>