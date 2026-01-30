<?php
// pages/login.php
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('index.php');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Authenticate using the same logic as Admin but checking all users
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = ($user['role'] === 'admin'); // For compatibility
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

$pageTitle = "Sign In";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-slate-50 flex items-center justify-center py-20 relative overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0">
        <img src="<?php echo base_url('assets/images/destinations/maldives.jpg'); ?>"
            class="w-full h-full object-cover opacity-10 blur-sm" alt="Login Background">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-slate-50/90 to-slate-50/80"></div>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md p-6 relative z-10 animate-fade-in-up">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-heading font-bold text-slate-900 mb-2">Welcome Back</h1>
            <p class="text-slate-500">Sign in to access your bespoke itinerary.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl relative p-10">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-secondary/5 rounded-full blur-2xl pointer-events-none">
            </div>

            <form id="login-form" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" name="email" required placeholder="name@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-slate-700 ml-1">Password</label>
                        <a href="#" class="text-xs text-primary hover:underline transition">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                    </div>
                </div>

                <?php if ($error): ?>
                    <div id="error-msg"
                        class="bg-red-50 text-red-600 text-sm p-3 rounded-lg border border-red-100 text-center">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary to-secondary hover:to-primary text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1 magnetic-btn">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">Don't have an account? <a href="<?php echo base_url('signup'); ?>"
                        class="text-primary font-bold hover:underline transition">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>