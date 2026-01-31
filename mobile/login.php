<?php
// mobile/login.php
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, go to profile
if (isLoggedIn()) {
    redirect(base_url('mobile/profile.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $db = Database::getInstance();
            $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                redirect(base_url('mobile/profile.php'));
            } else {
                $error = "Invalid email or password.";
            }
        } catch (Exception $e) {
            $error = "Login failed. Please try again.";
        }
    }
}

$pageTitle = "Login";
include __DIR__ . '/../includes/mobile_header.php';
?>

<div class="min-h-screen flex flex-col justify-center px-6 py-12 relative overflow-hidden">

    <!-- Background Decor -->
    <div
        class="absolute top-[-20%] right-[-20%] w-[80%] h-[50%] bg-primary/5 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[40%] bg-secondary/5 rounded-full blur-3xl pointer-events-none">
    </div>

    <div class="w-full max-w-sm mx-auto relative z-10">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-heading font-bold text-slate-900 mb-2">Welcome Back</h1>
            <p class="text-slate-500">Sign in to access your saved trips and bookings.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm flex items-center gap-3 border border-red-100">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div class="space-y-1">
                <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                <input type="email" name="email" required
                    class="w-full px-5 py-4 rounded-2xl bg-white border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all shadow-sm"
                    placeholder="name@example.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="space-y-1">
                <div class="flex justify-between px-1">
                    <label class="text-sm font-bold text-slate-700">Password</label>
                    <a href="#" class="text-xs font-semibold text-primary">Forgot?</a>
                </div>
                <input type="password" name="password" required
                    class="w-full px-5 py-4 rounded-2xl bg-white border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all shadow-sm"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-primary text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/30 active:scale-95 transition-all text-lg mt-4">
                Sign In
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-500 mb-4 text-sm">Don't have an account?</p>
            <a href="#"
                class="inline-block px-8 py-3 rounded-full border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">
                Create Account
            </a>
        </div>

        <div class="mt-8 text-center">
            <div class="text-xs text-slate-400">Demo: demo@example.com / password</div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>