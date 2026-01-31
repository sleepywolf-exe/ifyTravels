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
    <title>Partner Portal - <?php echo get_setting('site_name', 'ifyTravels'); ?></title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            outline: none;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #1a202c inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden bg-slate-900 text-white relative">

    <!-- Cinematic Background -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo base_url('assets/images/destinations/maldives.jpg'); ?>"
            class="w-full h-full object-cover opacity-60 animate-kenburns"
            style="animation: kenburns 40s infinite alternate;">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 md:p-8">

        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">

            <!-- Branding / Welcome (Hidden on Mobile small) -->
            <div class="hidden lg:block space-y-8">
                <a href="<?php echo base_url(''); ?>" class="inline-block mb-4 hover:opacity-80 transition-opacity">
                    <img src="<?php echo base_url('assets/images/logo-white.png'); ?>" alt="Logo" class="h-10">
                </a>

                <h1 class="text-6xl font-heading font-medium leading-tight text-white mb-6">
                    Curate the <br> <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-amber-500 font-bold italic pr-2">Extraordinary.</span>
                </h1>

                <div class="space-y-6">
                    <div
                        class="flex items-center gap-4 bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/5 max-w-sm">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-tr from-amber-400 to-orange-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            %
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">High Commission</h3>
                            <p class="text-slate-400 text-sm">Earn up to 15% on premium bookings.</p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/5 max-w-sm">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Instant Payouts</h3>
                            <p class="text-slate-400 text-sm">Track real-time earnings & withdraw anytime.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex items-center gap-3 opacity-60">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border border-slate-900" src="https://i.pravatar.cc/100?img=33"
                            alt="">
                        <img class="w-8 h-8 rounded-full border border-slate-900" src="https://i.pravatar.cc/100?img=47"
                            alt="">
                        <img class="w-8 h-8 rounded-full border border-slate-900" src="https://i.pravatar.cc/100?img=12"
                            alt="">
                    </div>
                    <span class="text-sm font-medium">Join 2,000+ top curators</span>
                </div>
            </div>

            <!-- Login Card -->
            <div class="glass-card p-8 md:p-10 rounded-3xl animate__animated animate__fadeInRight">

                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold font-heading text-white mb-2">Partner Portal</h2>
                    <p class="text-slate-400 text-sm">Welcome back. Please login to continue.</p>
                </div>

                <?php if ($error): ?>
                    <div
                        class="bg-red-500/10 border border-red-500/20 text-red-200 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-5">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Email</label>
                        <input type="email" name="email" required
                            class="form-input w-full px-4 py-3.5 rounded-xl transition-all"
                            placeholder="name@agency.com">
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Password</label>
                            <a href="forgot_password.php"
                                class="text-xs text-amber-400 hover:text-amber-300 transition-colors">Forgot?</a>
                        </div>
                        <input type="password" name="password" required
                            class="form-input w-full px-4 py-3.5 rounded-xl transition-all" placeholder="••••••••••••">
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/20 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 mt-4">
                        Access Dashboard
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-white/10 text-center">
                    <p class="text-slate-400 text-sm mb-3">Not a partner yet?</p>
                    <a href="<?php echo base_url('pages/partner-program.php'); ?>"
                        class="inline-block px-6 py-2 rounded-full border border-white/20 text-white font-medium hover:bg-white/10 transition-colors text-sm">
                        Apply Now
                    </a>
                </div>
            </div>

        </div>

        <div class="absolute bottom-6 left-0 w-full text-center lg:text-left lg:px-12 text-xs text-slate-500 z-10">
            &copy; <?php echo date('Y'); ?> ifyTravels. All rights reserved.
        </div>
    </div>

    <style>
        @keyframes kenburns {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }
    </style>
</body>

</html>