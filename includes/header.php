<?php
// Ensure functions and data are loaded
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../data/loader.php';
?>
<!DOCTYPE html>
<?php
// Check for Affiliate Referral
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $refCode = sanitize_input($_GET['ref']);

    try {
        $db = Database::getInstance();
        $aff = $db->fetch("SELECT id FROM affiliates WHERE code = ? AND status = 'active'", [$refCode]);

        if ($aff) {
            $affId = $aff['id'];
            $_SESSION['affiliate_id'] = $affId;
            setcookie('affiliate_id', $affId, time() + (86400 * 30), "/");

            try {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $referrer = $_SERVER['HTTP_REFERER'] ?? '';
                $oneMinuteAgo = date('Y-m-d H:i:s', time() - 60);
                $recent = $db->fetch("SELECT id FROM referral_clicks WHERE affiliate_id = ? AND ip_address = ? AND created_at > ?", [$affId, $ip, $oneMinuteAgo]);

                if (!$recent) {
                    $db->execute(
                        "INSERT INTO referral_clicks (affiliate_id, ip_address, user_agent, referrer_url) VALUES (?, ?, ?, ?)",
                        [$affId, $ip, $ua, $referrer]
                    );
                }
            } catch (Exception $e) {
                // Ignore tracking errors
            }
        }
    } catch (Exception $e) {
        error_log("Affiliate Tracking Error: " . $e->getMessage());
    }
} else {
    if (!isset($_SESSION['affiliate_id']) && isset($_COOKIE['affiliate_id'])) {
        $cookieAffId = intval($_COOKIE['affiliate_id']);
        try {
            $db = Database::getInstance();
            $aff = $db->fetch("SELECT id FROM affiliates WHERE id = ? AND status = 'active'", [$cookieAffId]);
            if ($aff) {
                $_SESSION['affiliate_id'] = $aff['id'];
                setcookie('affiliate_id', $aff['id'], time() + (86400 * 30), "/");
            } else {
                setcookie('affiliate_id', '', time() - 3600, "/");
            }
        } catch (Exception $e) { }
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'ifyTravels') : get_setting('site_name', 'ifyTravels'); ?>
    </title>
    
    <!-- SEO & Meta -->
    <?php
    $metaTitle = isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'ifyTravels') : get_setting('site_name', 'ifyTravels');
    $metaDesc = isset($pageDescription) ? $pageDescription : get_setting('meta_description', 'Discover luxury travel packages and unforgettable destinations with IfyTravels.');
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $metaUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"], '?');
    $metaImage = isset($pageImage) ? base_url($pageImage) : base_url('assets/images/logo-color.png');
    ?>

    <link rel="canonical" href="<?php echo $metaUrl; ?>">
    <meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(get_setting('meta_keywords', 'travel, tours, luxury, holidays')); ?>">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- OG Meta -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $metaUrl; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta property="og:image" content="<?php echo $metaImage; ?>">

    <!-- Fonts: Poppins, Outfit, and Playfair Display (Luxury) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- GSAP & Lenis (Smooth Scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.29/dist/lenis.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: {
                            primary: '#0F766E', // Deep Teal
                            secondary: '#D97706', // Amber Gold
                            charcoal: '#111827', // Dark Background
                            dark: '#0f172a'
                        },
                        fontFamily: {
                            heading: ['Playfair Display', 'serif'], // Luxury Font
                            body: ['Outfit', 'sans-serif'],
                            sans: ['Poppins', 'sans-serif']
                        }
                    }
                }
            }
        };
    </script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glassmorphism.min.css'); ?>?v=<?php echo time(); ?>">

    <style>
        /* Lenis Smooth Scroll */
        html.lenis { width: 100%; height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto; }
        .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
        .lenis.lenis-stopped { overflow: hidden; }
        .lenis.lenis-scrolling iframe { pointer-events: none; }
        
        .flatpickr-calendar { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; }
        .flatpickr-day.selected { background: #0F766E !important; border-color: #0F766E !important; }
    </style>
</head>

<body class="font-body bg-charcoal text-white antialiased selection:bg-secondary selection:text-white">

    <!-- FLOATING GLASS PILL NAVIGATION -->
    <nav id="navbar" class="fixed top-6 left-1/2 transform -translate-x-1/2 w-[90%] max-w-6xl z-50 transition-all duration-300 glass-pill rounded-full px-6 py-3 flex items-center justify-between">
        
        <!-- Logo -->
        <a href="<?php echo base_url(); ?>" class="flex items-center gap-2 group">
            <div class="w-10 h-10 bg-gradient-to-br from-secondary to-yellow-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:rotate-12 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xl font-heading font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                ify<span class="text-secondary">Travels</span>
            </span>
        </a>

        <!-- Desktop Links -->
        <div class="hidden md:flex items-center gap-8">
            <a href="<?php echo base_url(); ?>" class="text-gray-300 hover:text-white text-sm font-medium tracking-wide transition-colors relative group">
                Home
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/destinations.php'); ?>" class="text-gray-300 hover:text-white text-sm font-medium tracking-wide transition-colors relative group">
                Destinations
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/packages.php'); ?>" class="text-gray-300 hover:text-white text-sm font-medium tracking-wide transition-colors relative group">
                Packages
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/contact.php'); ?>" class="text-gray-300 hover:text-white text-sm font-medium tracking-wide transition-colors relative group">
                Contact
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-secondary transition-all group-hover:w-full"></span>
            </a>
        </div>

        <!-- Auth / Action Buttons -->
        <div class="hidden md:flex items-center gap-4">
            <?php if (isLoggedIn()): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 text-white hover:text-secondary transition-colors">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random" 
                             alt="Profile" 
                             class="w-9 h-9 rounded-full border-2 border-white/20">
                        <span class="font-medium text-sm"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 top-full mt-2 w-48 py-2 bg-charcoal/90 backdrop-blur-md rounded-xl shadow-xl border border-white/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2">
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Admin Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo base_url('user/dashboard.php'); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Dashboard</a>
                            <a href="<?php echo base_url('user/bookings.php'); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Bookings</a>
                        <?php endif; ?>
                        <div class="h-px bg-white/10 my-1"></div>
                        <a href="<?php echo base_url('auth/logout.php'); ?>" class="block px-4 py-2 text-sm text-red-400 hover:bg-white/5 hover:text-red-300">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>" class="text-white hover:text-secondary font-medium text-sm transition-colors">Login</a>
                <a href="<?php echo base_url('register'); ?>" class="bg-secondary hover:bg-yellow-600 text-white px-5 py-2 rounded-full font-medium text-sm transition-all shadow-lg hover:shadow-orange-500/20">
                    Sign Up
                </a>
            <?php endif; ?>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="md:hidden text-white hover:text-secondary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 bg-charcoal/95 backdrop-blur-xl z-[60] transform translate-x-full transition-transform duration-300 flex flex-col items-center justify-center space-y-8">
        <button id="close-menu-btn" class="absolute top-8 right-8 text-white/50 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <a href="<?php echo base_url(); ?>" class="text-2xl font-heading text-white hover:text-secondary">Home</a>
        <a href="<?php echo base_url('pages/destinations.php'); ?>" class="text-2xl font-heading text-white hover:text-secondary">Destinations</a>
        <a href="<?php echo base_url('pages/packages.php'); ?>" class="text-2xl font-heading text-white hover:text-secondary">Packages</a>
        <a href="<?php echo base_url('pages/contact.php'); ?>" class="text-2xl font-heading text-white hover:text-secondary">Contact</a>

        <?php if (isLoggedIn()): ?>
            <div class="h-px w-20 bg-white/10"></div>
            <a href="<?php echo base_url('user/dashboard.php'); ?>" class="text-xl text-gray-400 hover:text-white">Dashboard</a>
            <a href="<?php echo base_url('auth/logout.php'); ?>" class="text-xl text-red-400">Logout</a>
        <?php else: ?>
            <div class="flex flex-col gap-4 w-full px-10">
                <a href="<?php echo base_url('login'); ?>" class="w-full text-center border border-white/20 py-3 rounded-full text-white">Login</a>
                <a href="<?php echo base_url('register'); ?>" class="w-full text-center bg-secondary py-3 rounded-full text-white">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Init Smooth Scroll (Lenis)
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            direction: 'vertical',
            gestureDirection: 'vertical',
            smooth: true,
            mouseMultiplier: 1,
            smoothTouch: false,
            touchMultiplier: 2,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // Mobile Menu Logic
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        function toggleMenu() {
            mobileMenu.classList.toggle('translate-x-full');
            document.body.classList.toggle('overflow-hidden');
        }

        if(mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
        if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
    </script>