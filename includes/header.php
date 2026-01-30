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

    <!-- DECONSTRUCTED FLOATING NAVIGATION -->

    <!-- 1. Left: Brand Identity (Fixed) -->
    <a href="<?php echo base_url(); ?>" class="fixed top-6 left-6 z-[60] flex items-center gap-2 group">
        <div
            class="relative w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-md rounded-full border border-white/20 shadow-lg group-hover:bg-white/20 transition-all duration-300">
            <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels"
                class="w-8 h-8 object-contain">
        </div>
        <span
            class="hidden md:block font-heading font-bold text-xl text-slate-800 tracking-tight group-hover:text-primary transition-colors">ifyTravels</span>
    </a>

    <!-- 2. Center: Floating Nav Capsule (Fixed) -->
    <nav id="floating-nav"
        class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[50] transition-all duration-500 ease-out">
        <div
            class="relative bg-white/80 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-slate-200/50 rounded-full px-2 py-2 flex items-center gap-1">

            <!-- Sliding Tab Background -->
            <div id="nav-highlight"
                class="absolute h-10 bg-primary/10 rounded-full transition-all duration-300 ease-out -z-10 opacity-0">
            </div>

            <!-- Links -->
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
                    class="nav-link relative px-5 py-2.5 rounded-full text-sm font-semibold tracking-wide transition-all duration-300 <?php echo $isActive ? 'text-primary' : 'text-slate-600 hover:text-slate-900'; ?>"
                    onmouseenter="moveHighlight(this)" onmouseleave="resetHighlight()">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- 3. Right: Auth & Tools (Fixed) -->
    <div class="fixed top-6 right-6 z-[60] flex items-center gap-4">

        <!-- Auth Buttons (Desktop) -->
        <div class="hidden md:flex items-center gap-3">
            <?php if (isLoggedIn()): ?>
                <div class="relative group">
                    <button
                        class="flex items-center gap-3 bg-white/80 backdrop-blur-lg px-4 py-2 rounded-full border border-white/50 shadow-sm hover:shadow-md transition-all">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=0F766E&color=fff"
                            alt="Profile" class="w-8 h-8 rounded-full border-2 border-white">
                        <span
                            class="font-medium text-sm text-slate-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </button>
                    <!-- Dropdown -->
                    <div
                        class="absolute right-0 top-full mt-4 w-56 py-3 bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-4">
                        <div class="px-4 py-2 border-b border-slate-100 mb-2">
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Account</span>
                        </div>
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo base_url('admin/dashboard.php'); ?>"
                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors mx-2 rounded-lg">Admin
                                Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo base_url('user/dashboard.php'); ?>"
                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors mx-2 rounded-lg">My
                                Dashboard</a>
                            <a href="<?php echo base_url('user/bookings.php'); ?>"
                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors mx-2 rounded-lg">My
                                Bookings</a>
                        <?php endif; ?>
                        <div class="h-px bg-slate-100 my-2 mx-4"></div>
                        <a href="<?php echo base_url('auth/logout.php'); ?>"
                            class="block px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors mx-2 rounded-lg">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>"
                    class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors bg-white/50 backdrop-blur-md rounded-full hover:bg-white/80 border border-transparent hover:border-slate-200">
                    Login
                </a>
                <a href="<?php echo base_url('register'); ?>"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-full hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 magnetic-btn">
                    Sign Up
                </a>
            <?php endif; ?>
        </div>

        <!-- Mobile Menu Trigger -->
        <button id="mobile-menu-btn"
            class="md:hidden w-12 h-12 flex items-center justify-center bg-white/80 backdrop-blur-md rounded-full border border-white/50 shadow-lg text-slate-800 hover:bg-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h10" />
            </svg>
        </button>
    </div>

    <!-- FULLSCREEN IMMERSIVE MOBILE MENU -->
    <div id="mobile-menu"
        class="fixed inset-0 bg-slate-50 z-[100] transform translate-y-full transition-transform duration-500 cubic-bezier(0.77, 0, 0.175, 1) flex flex-col">

        <!-- Header -->
        <div class="px-6 py-6 flex items-center justify-between border-b border-slate-100">
            <span class="font-heading font-bold text-2xl text-slate-900">ifyTravels</span>
            <button id="close-menu-btn"
                class="w-12 h-12 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200 hover:text-slate-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Links Container -->
        <div class="flex-1 px-8 py-12 flex flex-col justify-center space-y-2">
            <?php
            $mobileLinks = [
                'Home' => '',
                'Destinations' => 'pages/destinations.php',
                'Packages' => 'pages/packages.php',
                'Contact' => 'pages/contact.php'
            ];
            $delay = 0;
            foreach ($mobileLinks as $label => $url):
                $delay += 0.1;
                ?>
                <a href="<?php echo base_url($url); ?>"
                    class="mobile-link text-5xl md:text-7xl font-heading font-bold text-slate-300 hover:text-slate-900 transition-colors opacity-0 transform translate-y-10"
                    style="transition-delay: <?php echo $delay; ?>s">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Footer Actions -->
        <div class="p-8 border-t border-slate-100 flex flex-col gap-4">
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo base_url('user/dashboard.php'); ?>"
                    class="w-full py-4 bg-slate-900 text-white font-bold text-center rounded-2xl shadow-xl">Dashboard</a>
                <a href="<?php echo base_url('auth/logout.php'); ?>"
                    class="w-full py-4 bg-red-50 text-red-500 font-bold text-center rounded-2xl">Logout</a>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-4">
                    <a href="<?php echo base_url('login'); ?>"
                        class="py-4 bg-white border border-slate-200 text-slate-900 font-bold text-center rounded-2xl">Login</a>
                    <a href="<?php echo base_url('register'); ?>"
                        class="py-4 bg-primary text-white font-bold text-center rounded-2xl shadow-lg shadow-primary/20">Sign
                        Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

        // --- NEW NAV INTERACTION LOGIC --- //

        const highlight = document.getElementById('nav-highlight');
        const activeLink = document.querySelector('.nav-link.text-primary'); // Find active link

        function moveHighlight(el) {
            if (!highlight) return;
            const rect = el.getBoundingClientRect();
            const parentRect = el.parentElement.getBoundingClientRect();

            highlight.style.width = `${rect.width}px`;
            highlight.style.transform = `translateX(${rect.left - parentRect.left}px)`;
            highlight.style.opacity = '1';
        }

        function resetHighlight() {
            if (!highlight) return;
            // Return to active link if exists
            if (activeLink) {
                moveHighlight(activeLink);
            } else {
                highlight.style.opacity = '0';
            }
        }

        // Init Highlight Position
        window.addEventListener('load', resetHighlight);
        window.addEventListener('resize', resetHighlight);


        // --- SCROLL SHRINK EFFECT --- //
        const navCapsule = document.getElementById('floating-nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navCapsule.classList.add('scale-90', 'origin-top');
            } else {
                navCapsule.classList.remove('scale-90', 'origin-top');
            }
        });


        // --- MOBILE MENU LOGIC --- //
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            const isOpen = mobileMenu.classList.contains('translate-y-0');

            if (isOpen) {
                // Close
                mobileMenu.classList.remove('translate-y-0');
                mobileMenu.classList.add('translate-y-full');
                document.body.classList.remove('overflow-hidden');

                // Reset links
                mobileLinks.forEach(link => {
                    link.classList.remove('opacity-100', 'translate-y-0');
                    link.classList.add('opacity-0', 'translate-y-10');
                });
            } else {
                // Open
                mobileMenu.classList.remove('translate-y-full');
                mobileMenu.classList.add('translate-y-0');
                document.body.classList.add('overflow-hidden');

                // Animate links in
                setTimeout(() => {
                    mobileLinks.forEach((link, index) => {
                        setTimeout(() => {
                            link.classList.remove('opacity-0', 'translate-y-10');
                            link.classList.add('opacity-100', 'translate-y-0');
                        }, index * 100);
                    });
                }, 300);
            }
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