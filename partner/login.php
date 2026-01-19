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
    <title>Partner Login - <?php echo get_setting('site_name', 'ifyTravels'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                        charcoal: '#1F2937'
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="h-screen w-full bg-gray-50 flex overflow-hidden">

    <!-- Left Side: Image & Branding -->
    <div class="hidden lg:flex w-1/2 relative bg-gray-900 text-white flex-col justify-between p-16 overflow-hidden">
        <!-- Dynamic Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo base_url('assets/images/destinations/ladakh.jpg'); ?>"
                class="w-full h-full object-cover opacity-60 scale-105 animate-pulse-slow"
                style="animation-duration: 20s;">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
        </div>

        <!-- Logo -->
        <div class="relative z-10 animate__animated animate__fadeInDown">
            <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl inline-block border border-white/10">
                <img src="<?php echo base_url('assets/images/logo-white.png'); ?>" alt="Logo" class="h-10">
            </div>
        </div>

        <!-- Quote / Welcome -->
        <div class="relative z-10 max-w-lg animate__animated animate__fadeInUp animate__delay-1s">
            <h2 class="text-5xl font-bold font-heading leading-tight mb-6">Grow your travel business with us.</h2>
            <p class="text-lg text-gray-300 font-light leading-relaxed">Join our network of elite travel partners and
                offer your customers unforgettable experiences.</p>

            <div class="mt-8 flex items-center gap-4">
                <div class="flex -space-x-2">
                    <img class="w-10 h-10 rounded-full border-2 border-gray-900" src="https://i.pravatar.cc/100?img=1"
                        alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-gray-900" src="https://i.pravatar.cc/100?img=5"
                        alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-gray-900" src="https://i.pravatar.cc/100?img=8"
                        alt="">
                    <div
                        class="w-10 h-10 rounded-full border-2 border-gray-900 bg-gray-700 flex items-center justify-center text-xs font-bold">
                        +2k</div>
                </div>
                <span class="text-sm font-medium text-gray-400">Partners Trust Us</span>
            </div>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-6 md:p-12 relative">
        <!-- Mobile Background (Visible only on small screens) -->
        <div class="absolute inset-0 lg:hidden z-0">
            <img src="<?php echo base_url('assets/images/destinations/kashmir.jpg'); ?>"
                class="w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-tr from-white via-white/90 to-white/60 backdrop-blur-sm"></div>
        </div>

        <div class="w-full max-w-md relative z-10 animate__animated animate__fadeInRight">

            <!-- Mobile Logo -->
            <div class="lg:hidden mb-10 text-center">
                <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo" class="h-10 mx-auto">
            </div>

            <div class="text-center lg:text-left mb-10">
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Welcome Back</h1>
                <p class="text-gray-500">Enter your credentials to access your dashboard.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r mb-6 animate__animated animate__headShake">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div class="group">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" name="email" required
                            class="block w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-300"
                            placeholder="partner@ifytravels.com">
                    </div>
                </div>

                <div class="group">
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-semibold text-gray-700">Password</label>
                        <a href="#" class="text-xs font-semibold text-primary hover:text-teal-700 transition">Forgot
                            password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" required
                            class="block w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-300"
                            placeholder="••••••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="relative w-full bg-gradient-to-r from-primary to-teal-800 text-white font-bold py-4 rounded-2xl shadow-lg hover:shadow-xl hover:shadow-primary/30 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            Sign In to Portal
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                        <div
                            class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-700 transform -skew-x-12 -translate-x-full">
                        </div>
                    </button>
                </div>
            </form>

            <div class="mt-10 text-center">
                <p class="text-gray-500 mb-4">Don't have an account?</p>
                <a href="<?php echo base_url('pages/partner-program.php'); ?>"
                    class="inline-block w-full py-4 rounded-2xl border-2 border-gray-100 text-gray-700 font-bold hover:border-primary hover:text-primary transition-all duration-300">
                    Apply for Partnership
                </a>
            </div>

            <div class="mt-8 text-center">
                <a href="<?php echo base_url(''); ?>"
                    class="text-xs text-gray-400 hover:text-gray-600 transition flex items-center justify-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to ifyTravels.com
                </a>
            </div>
        </div>
    </div>

</body>

</html>