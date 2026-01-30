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
        } catch (Exception $e) {
        }
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
    <meta name="keywords"
        content="<?php echo htmlspecialchars(get_setting('meta_keywords', 'travel, tours, luxury, holidays')); ?>">
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
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
                            charcoal: '#111827', // Dark Background (Legacy)
                            dark: '#0f172a',
                            light: '#f8fafc', // Soft White
                            'dark-text': '#0f172a', // Dark Slate
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
        html.lenis {
            width: 100%;
            height: auto;
        }

        .lenis.lenis-smooth {
            scroll-behavior: auto;
        }

        .lenis.lenis-smooth [data-lenis-prevent] {
            overscroll-behavior: contain;
        }

        .lenis.lenis-stopped {
            overflow: hidden;
        }

        .lenis.lenis-scrolling iframe {
            pointer-events: none;
        }

        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .flatpickr-day.selected {
            background: #0F766E !important;
            border-color: #0F766E !important;
        }

        /* Creative Animations */
        .reveal-text {
            opacity: 0;
            transform: translateY(30px);
        }

        /* Preloader */
        #preloader {
            transition: opacity 0.5s ease;
        }

        /* Custom Flatpickr Theme */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(15, 118, 110, 0.1) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
            font-family: 'Outfit', sans-serif !important;
            border-radius: 16px !important;
            padding: 10px !important;
        }

        .flatpickr-day {
            border-radius: 8px !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day:hover,
        .flatpickr-day:focus {
            background: #0F766E !important;
            border-color: #0F766E !important;
            color: white !important;
            box-shadow: 0 4px 6px rgba(15, 118, 110, 0.3);
        }

        .flatpickr-day.today {
            border-color: #D97706 !important;
        }

        .flatpickr-weekday {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        .flatpickr-months .flatpickr-month {
            color: #0F766E !important;
            fill: #0F766E !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
        }

        .flatpickr-day.inRange {
            box-shadow: -5px 0 0 #e2e8f0, 5px 0 0 #e2e8f0 !important;
            background: #e2e8f0 !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
    </style>
</head>

<body
    class="font-body bg-slate-50 text-slate-900 antialiased selection:bg-secondary selection:text-white overflow-x-hidden">

    <!-- PRELOADER -->
    <div id="preloader" class="fixed inset-0 z-[100] bg-slate-50 flex flex-col items-center justify-center">
        <div class="relative w-24 h-24 mb-4">
            <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-primary rounded-full border-t-transparent animate-spin"></div>
            <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="Loading"
                class="absolute inset-0 m-auto w-12 h-auto opacity-80">
        </div>
        <span class="text-primary font-heading font-bold text-lg tracking-widest animate-pulse">ifyTravels</span>
    </div>

    <!-- FLOATING GLASS PILL NAVIGATION -->
    <nav id="navbar"
        class="fixed top-6 left-1/2 transform -translate-x-1/2 w-[90%] max-w-6xl z-50 transition-all duration-300 bg-white/70 backdrop-blur-xl border border-white/40 shadow-lg shadow-gray-200/20 rounded-full px-6 py-3 flex items-center justify-between">

        <!-- Logo -->
        <a href="<?php echo base_url(); ?>" class="flex items-center gap-2 group">
            <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels Logo"
                class="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
        </a>

        <!-- Desktop Links -->
        <div class="hidden md:flex items-center gap-8">
            <a href="<?php echo base_url(); ?>"
                class="text-gray-600 hover:text-primary text-sm font-medium tracking-wide transition-colors relative group py-2">
                Home
                <span
                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary to-secondary transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/destinations.php'); ?>"
                class="text-gray-600 hover:text-primary text-sm font-medium tracking-wide transition-colors relative group py-2">
                Destinations
                <span
                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary to-secondary transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/packages.php'); ?>"
                class="text-gray-600 hover:text-primary text-sm font-medium tracking-wide transition-colors relative group py-2">
                Packages
                <span
                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary to-secondary transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('pages/contact.php'); ?>"
                class="text-gray-600 hover:text-primary text-sm font-medium tracking-wide transition-colors relative group py-2">
                Contact
                <span
                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary to-secondary transition-all duration-300 group-hover:w-full"></span>
            </a>
        </div>

        <!-- Auth / Action Buttons -->
        <div class="hidden md:flex items-center gap-4">
            <?php if (isLoggedIn()): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 text-gray-700 hover:text-primary transition-colors">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random"
                            alt="Profile"
                            class="w-9 h-9 rounded-full border-2 border-primary/20 hover:border-primary transition-colors p-0.5">
                        <span class="font-medium text-sm"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </button>
                    <!-- Dropdown -->
                    <div
                        class="absolute right-0 top-full mt-2 w-48 py-2 bg-white/90 backdrop-blur-md rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2">
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo base_url('admin/dashboard.php'); ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary rounded-lg mx-2">Admin
                                Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo base_url('user/dashboard.php'); ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary rounded-lg mx-2">My
                                Dashboard</a>
                            <a href="<?php echo base_url('user/bookings.php'); ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary rounded-lg mx-2">My
                                Bookings</a>
                        <?php endif; ?>
                        <div class="h-px bg-gray-100 my-1 mx-2"></div>
                        <a href="<?php echo base_url('auth/logout.php'); ?>"
                            class="block px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg mx-2">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>"
                    class="text-gray-600 hover:text-primary font-medium text-sm transition-colors">Login</a>
                <a href="<?php echo base_url('register'); ?>"
                    class="bg-gradient-to-r from-secondary to-yellow-500 hover:from-yellow-500 hover:to-secondary text-white px-6 py-2.5 rounded-full font-bold text-sm transition-all shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 magnetic-btn">
                    Sign Up
                </a>
            <?php endif; ?>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-primary transition-colors p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu"
        class="fixed inset-0 bg-white/98 backdrop-blur-xl z-[60] transform translate-x-full transition-transform duration-300 flex flex-col items-center justify-center space-y-8">
        <button id="close-menu-btn"
            class="absolute top-8 right-8 text-slate-400 hover:text-slate-800 transition-colors bg-slate-100 rounded-full p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <a href="<?php echo base_url(); ?>"
            class="text-3xl font-heading font-bold text-slate-800 hover:text-primary transition-colors">Home</a>
        <a href="<?php echo base_url('pages/destinations.php'); ?>"
            class="text-3xl font-heading font-bold text-slate-800 hover:text-primary transition-colors">Destinations</a>
        <a href="<?php echo base_url('pages/packages.php'); ?>"
            class="text-3xl font-heading font-bold text-slate-800 hover:text-primary transition-colors">Packages</a>
        <a href="<?php echo base_url('pages/contact.php'); ?>"
            class="text-3xl font-heading font-bold text-slate-800 hover:text-primary transition-colors">Contact</a>

        <?php if (isLoggedIn()): ?>
            <div class="h-px w-20 bg-slate-200"></div>
            <a href="<?php echo base_url('user/dashboard.php'); ?>"
                class="text-xl text-slate-600 hover:text-primary font-medium">Dashboard</a>
            <a href="<?php echo base_url('auth/logout.php'); ?>"
                class="text-xl text-red-500 font-medium hover:text-red-600">Logout</a>
        <?php else: ?>
            <div class="flex flex-col gap-4 w-full px-10 max-w-sm mt-8">
                <a href="<?php echo base_url('login'); ?>"
                    class="w-full text-center border-2 border-slate-200 py-3.5 rounded-xl text-slate-700 font-bold hover:border-primary hover:text-primary transition-colors">Login</a>
                <a href="<?php echo base_url('register'); ?>"
                    class="w-full text-center bg-secondary py-3.5 rounded-xl text-white font-bold shadow-lg shadow-orange-500/20 hover:bg-yellow-600 transition-colors">Sign
                    Up</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Preloader Logic
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                gsap.to(preloader, {
                    opacity: 0,
                    duration: 0.8,
                    delay: 0.2,
                    ease: "power2.inOut",
                    onComplete: () => {
                        preloader.style.display = 'none';
                    }
                });
            }
        });

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

        if (mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
        if (closeBtn) closeBtn.addEventListener('click', toggleMenu);

        // GSAP Animations
        document.addEventListener("DOMContentLoaded", (event) => {
            gsap.registerPlugin(ScrollTrigger);

            // Text Reveals
            const revealElements = document.querySelectorAll(".reveal-text");
            revealElements.forEach((element) => {
                gsap.to(element, {
                    scrollTrigger: {
                        trigger: element,
                        start: "top 85%",
                        toggleActions: "play none none reverse"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 1,
                    ease: "power3.out"
                });
            });

            // Magnetic Buttons
            const magneticBtns = document.querySelectorAll(".magnetic-btn");
            magneticBtns.forEach((btn) => {
                btn.addEventListener("mousemove", (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    gsap.to(btn, { duration: 0.3, x: x * 0.3, y: y * 0.3, ease: "power2.out" });
                });
                btn.addEventListener("mouseleave", () => {
                    gsap.to(btn, { duration: 0.3, x: 0, y: 0, ease: "elastic.out(1, 0.3)" });
                });
            });
        });
    </script>