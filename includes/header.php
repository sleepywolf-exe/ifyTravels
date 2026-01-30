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

    <!-- Fonts: Plus Jakarta Sans (Modern, Geometric, Premium) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
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
                            heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                            body: ['"Plus Jakarta Sans"', 'sans-serif'],
                            sans: ['"Plus Jakarta Sans"', 'sans-serif']
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

        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Flatpickr Luxury Theme (Teal/Glass) */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 20px 40px -10px rgba(15, 118, 110, 0.15) !important;
            border-radius: 1.5rem !important;
            padding: 1rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .flatpickr-months .flatpickr-month {
            background: transparent !important;
            color: #0f172a !important;
            fill: #0f172a !important;
            margin-bottom: 0.5rem !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
            font-size: 1.2rem !important;
        }

        .flatpickr-weekdays {
            margin-bottom: 0.5rem !important;
        }

        span.flatpickr-weekday {
            color: #64748b !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
        }

        .flatpickr-day {
            border-radius: 0.75rem !important;
            color: #334155 !important;
            font-weight: 500 !important;
            font-size: 1rem !important;
            transition: all 0.2s ease !important;
            border: 1px solid transparent !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day:hover {
            background: #0F766E !important;
            color: #fff !important;
            border-color: #0F766E !important;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3) !important;
            font-weight: bold !important;
        }

        .flatpickr-day.today {
            border-color: #0F766E !important;
            color: #0F766E !important;
            background: rgba(15, 118, 110, 0.05) !important;
        }

        .flatpickr-day.today:hover {
            color: white !important;
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

    <!-- HIGH-END MODERN HEADER (V4) -->
    <header id="main-header" class="fixed top-4 left-0 right-0 z-50 flex justify-center transition-all duration-300">
        <div id="header-capsule"
            class="w-[95%] max-w-7xl bg-white/80 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-slate-200/40 rounded-2xl px-3 py-3 transition-all duration-300 hover:bg-white/90">
            <div class="flex items-center justify-between">

                <!-- Logo (Image Only) -->
                <a href="<?php echo base_url(); ?>" class="flex items-center gap-2 group px-2"
                    aria-label="ifyTravels Home">
                    <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels Logo" width="140"
                        height="45" loading="eager"
                        class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                </a>

                <!-- Desktop Navigation (Centered) -->
                <nav class="hidden md:flex items-center gap-1 bg-slate-100/50 rounded-full p-1 border border-white/50">
                    <?php
                    $navLinks = [
                        '' => 'Home',
                        'pages/destinations.php' => 'Destinations',
                        'pages/packages.php' => 'Packages',
                        'pages/contact.php' => 'Contact'
                    ];

                    foreach ($navLinks as $url => $label):
                        $isActive = (current_url() == base_url($url));
                        ?>
                        <a href="<?php echo base_url($url); ?>"
                            class="relative px-5 py-2 rounded-full text-[14px] font-sans font-medium transition-all duration-300 <?php echo $isActive ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'; ?>">
                            <?php echo $label; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Auth & Actions (Right) -->
                <div class="hidden md:flex items-center gap-3 px-2">
                    <?php if (isLoggedIn()): ?>
                        <div class="relative group">
                            <button
                                class="flex items-center gap-2 pl-1 pr-3 py-1.5 rounded-full hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=0F766E&color=fff"
                                    alt="Profile" class="w-8 h-8 rounded-full shadow-sm">
                                <span
                                    class="font-sans font-medium text-sm text-slate-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <!-- Dropdown -->
                            <div
                                class="absolute right-0 top-full mt-2 w-56 bg-white/95 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right group-hover:scale-100 scale-95 p-2">
                                <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider font-sans">
                                        Account</p>
                                </div>
                                <?php if (isAdmin()): ?>
                                    <a href="<?php echo base_url('admin/dashboard.php'); ?>"
                                        class="block px-3 py-2 text-sm font-sans font-medium text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">Admin
                                        Dashboard</a>
                                <?php else: ?>
                                    <a href="<?php echo base_url('user/dashboard.php'); ?>"
                                        class="block px-3 py-2 text-sm font-sans font-medium text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">My
                                        Dashboard</a>
                                    <a href="<?php echo base_url('user/bookings.php'); ?>"
                                        class="block px-3 py-2 text-sm font-sans font-medium text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">My
                                        Bookings</a>
                                <?php endif; ?>
                                <div class="h-px bg-slate-50 my-1"></div>
                                <a href="<?php echo base_url('auth/logout.php'); ?>"
                                    class="block px-3 py-2 text-sm font-sans font-medium text-red-500 hover:bg-red-50 rounded-lg transition-colors">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo base_url('login'); ?>"
                            class="px-5 py-2 text-sm font-sans font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                            Login
                        </a>
                        <a href="<?php echo base_url('register'); ?>"
                            class="px-5 py-2 text-sm font-sans font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-full transition-all shadow-lg shadow-slate-900/20 hover:shadow-slate-900/30 transform hover:-translate-y-0.5">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn"
                    class="md:hidden p-2 text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors"
                    aria-label="Toggle mobile menu" aria-controls="mobile-menu" aria-expanded="false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu"
        class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-[60] md:hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
            <span class="font-heading font-bold text-xl text-slate-900">Menu</span>
            <button id="close-menu-btn"
                class="p-2 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="px-4 py-6 space-y-1">
            <?php foreach ($navLinks as $url => $label): ?>
                <a href="<?php echo base_url($url); ?>"
                    class="block px-4 py-3 text-slate-700 hover:bg-slate-50 hover:text-primary rounded-lg font-medium transition-colors">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- User Section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-200 bg-slate-50">
            <?php if (isLoggedIn()): ?>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-slate-200">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=0F766E&color=fff"
                            alt="Profile" class="w-10 h-10 rounded-full">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-slate-900 truncate">
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </p>
                            <p class="text-xs text-slate-500">View Profile</p>
                        </div>
                    </div>
                    <a href="<?php echo base_url('user/dashboard.php'); ?>"
                        class="block w-full py-3 px-4 bg-slate-900 text-white font-semibold text-center rounded-lg hover:bg-slate-800 transition-colors">
                        Dashboard
                    </a>
                    <a href="<?php echo base_url('auth/logout.php'); ?>"
                        class="block w-full py-3 px-4 bg-white border border-slate-200 text-red-600 font-semibold text-center rounded-lg hover:bg-red-50 transition-colors">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <a href="<?php echo base_url('login'); ?>"
                        class="block w-full py-3 px-4 bg-white border border-slate-200 text-slate-900 font-semibold text-center rounded-lg hover:bg-slate-50 transition-colors">
                        Login
                    </a>
                    <a href="<?php echo base_url('register'); ?>"
                        class="block w-full py-3 px-4 bg-primary text-white font-semibold text-center rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                        Sign Up
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay"
        class="fixed inset-0 bg-black/50 opacity-0 invisible transition-all duration-300 z-[55] md:hidden"></div>

    <!-- Scripts -->
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

        // Header Capsule Animation on Scroll
        const capsule = document.getElementById('header-capsule');
        let isScrolled = false;

        if (capsule) {
            window.addEventListener('scroll', () => {
                const shouldBeScrolled = window.scrollY > 20;

                if (shouldBeScrolled && !isScrolled) {
                    // Shrink
                    capsule.classList.remove('py-3', 'w-[95%]');
                    capsule.classList.add('py-2', 'w-[90%]', 'bg-white/95');
                    isScrolled = true;
                } else if (!shouldBeScrolled && isScrolled) {
                    // Expand
                    capsule.classList.add('py-3', 'w-[95%]');
                    capsule.classList.remove('py-2', 'w-[90%]', 'bg-white/95');
                    isScrolled = false;
                }
            }, { passive: true });
        }

        // Mobile Menu
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function openMobileMenu() {
            mobileMenu.classList.remove('translate-x-full');
            mobileOverlay.classList.remove('invisible', 'opacity-0');
            mobileOverlay.classList.add('visible', 'opacity-100');
            document.body.classList.add('overflow-hidden');
        }

        function closeMobileMenu() {
            mobileMenu.classList.add('translate-x-full');
            mobileOverlay.classList.remove('visible', 'opacity-100');
            mobileOverlay.classList.add('invisible', 'opacity-0');
            document.body.classList.remove('overflow-hidden');
        }

        if (mobileBtn) mobileBtn.addEventListener('click', openMobileMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMobileMenu);
        if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);

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