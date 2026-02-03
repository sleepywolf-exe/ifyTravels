<?php
// Ensure functions and data are loaded
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../data/loader.php';
require_once __DIR__ . '/classes/FacebookCAPI.php';

// Initialize CAPI
$fbCapi = new FacebookCAPI();
// Prepare User Data for CAPI (from Session if available)
$userData = [];
if (isLoggedIn()) {
    $userData['email'] = $_SESSION['user_email'] ?? '';
    // Add phone if available in session
}

// Track PageView Globally
$fbCapi->sendEvent('PageView', [], $userData);
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

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>">

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

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                        },
                        boxShadow: {
                            'creative': '0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 30px rgba(15, 118, 110, 0.15)',
                            'creative-hover': '0 35px 60px -15px rgba(0, 0, 0, 0.3), 0 0 40px rgba(15, 118, 110, 0.25)',
                            'neon': '0 0 20px rgba(15, 118, 110, 0.4)',
                        }
                    }
                }
            }
        };
    </script>
    <style>
        /* Global Depth Class */
        .depth-card {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 30px rgba(15, 118, 110, 0.15);
            transition: all 0.5s ease;
        }

        .depth-card:hover {
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3), 0 0 40px rgba(15, 118, 110, 0.25);
            transform: translateY(-5px);
        }
    </style>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        /* === PREMIUM BRAND-MATCHED FLATPICKR CALENDAR === */

        /* Calendar Container - Glassmorphism - COMPACT */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 2px solid rgba(15, 118, 110, 0.2) !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2), 0 0 25px rgba(15, 118, 110, 0.12) !important;
            border-radius: 16px !important;
            padding: 0 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            animation: fadeInCalendar 0.25s ease !important;
            width: auto !important;
            max-width: 320px !important;
            overflow: visible !important;
        }

        @keyframes fadeInCalendar {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Month Header - TEAL GRADIENT - COMPACT */
        .flatpickr-months {
            background: linear-gradient(135deg, #0F766E 0%, #0d6962 100%) !important;
            padding: 12px 16px !important;
            margin: 0 !important;
            border-radius: 14px 14px 0 0 !important;
            position: relative !important;
        }

        .flatpickr-month {
            color: white !important;
            fill: white !important;
            height: auto !important;
        }

        /* Month/Year Display - COMPACT */
        .flatpickr-current-month {
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: white !important;
            padding: 0.2rem 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 10 !important;
        }

        .flatpickr-current-month * {
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Month Dropdown - COMPACT */
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            border-radius: 8px !important;
            padding: 4px 10px !important;
            color: white !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            cursor: pointer !important;
            min-width: 110px !important;
            transition: all 0.2s ease !important;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            border-color: rgba(255, 255, 255, 0.6) !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            color: #0f172a !important;
            background: white !important;
        }

        /* Year Input - COMPACT */
        .flatpickr-current-month .numInputWrapper {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            border-radius: 8px !important;
            padding: 4px 8px !important;
            width: 65px !important;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .flatpickr-current-month input.cur-year {
            color: white !important;
            font-weight: 700 !important;
            background: transparent !important;
            border: none !important;
            font-size: 0.9rem !important;
            padding: 0 !important;
            text-align: center !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
        }

        .flatpickr-current-month .arrowUp,
        .flatpickr-current-month .arrowDown {
            display: none !important;
        }

        /* Navigation Arrows - COMPACT */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: white !important;
            fill: white !important;
            padding: 6px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            background: rgba(255, 255, 255, 0.15) !important;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            transform: scale(1.05) !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* Weekday Headers - COMPACT */
        .flatpickr-weekdays {
            background: transparent !important;
            margin: 10px 0 6px 0 !important;
            padding: 0 12px !important;
            height: auto !important;
        }

        span.flatpickr-weekday {
            color: #64748b !important;
            font-weight: 700 !important;
            font-size: 0.65rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            background: transparent !important;
        }

        /* Days Grid - COMPACT */
        .flatpickr-days {
            width: 100% !important;
            padding: 0 10px 12px 10px !important;
        }

        .dayContainer {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
        }

        /* DAY CELLS - COMPACT SIZE */
        .flatpickr-day {
            color: #0f172a !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            border: 2px solid transparent !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            margin: 2px !important;
            height: 34px !important;
            line-height: 34px !important;
            max-width: 34px !important;
            background: transparent !important;
        }

        /* Hover for Available Dates */
        .flatpickr-day:hover:not(.flatpickr-disabled):not(.selected):not(.prevMonthDay):not(.nextMonthDay) {
            background: rgba(15, 118, 110, 0.08) !important;
            border-color: rgba(15, 118, 110, 0.4) !important;
            transform: translateY(-2px) !important;
            color: #0F766E !important;
            box-shadow: 0 4px 8px rgba(15, 118, 110, 0.15) !important;
        }

        /* Selected Date - TEAL */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange {
            background: linear-gradient(135deg, #0F766E 0%, #0d6962 100%) !important;
            border-color: #0F766E !important;
            color: white !important;
            box-shadow: 0 8px 16px rgba(15, 118, 110, 0.4), 0 0 0 3px rgba(15, 118, 110, 0.1) !important;
            transform: scale(1.05) !important;
            font-weight: 900 !important;
        }

        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background: linear-gradient(135deg, #0d6962 0%, #0F766E 100%) !important;
            transform: scale(1.08) !important;
        }

        /* TODAY - Prominent ORANGE/AMBER */
        .flatpickr-day.today {
            border: 3px solid #D97706 !important;
            color: #D97706 !important;
            background: rgba(217, 119, 6, 0.12) !important;
            font-weight: 900 !important;
            font-size: 1.05rem !important;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1) !important;
        }

        .flatpickr-day.today:hover {
            background: rgba(217, 119, 6, 0.2) !important;
            color: #D97706 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3) !important;
        }

        .flatpickr-day.today.selected {
            background: linear-gradient(135deg, #D97706 0%, #b45309 100%) !important;
            border-color: #D97706 !important;
            color: white !important;
        }

        /* Disabled/Past Dates - LIGHT & FADED */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            cursor: not-allowed !important;
        }

        .flatpickr-day.flatpickr-disabled:hover {
            background: transparent !important;
            transform: none !important;
        }

        /* In Range Days */
        .flatpickr-day.inRange {
            background: rgba(15, 118, 110, 0.15) !important;
            border-color: rgba(15, 118, 110, 0.2) !important;
            box-shadow: none !important;
        }

        /* Time Picker (if enabled) */
        .flatpickr-time {
            border-top: 1px solid rgba(15, 118, 110, 0.1) !important;
            margin-top: 12px !important;
            padding-top: 12px !important;
        }

        .flatpickr-time input {
            background: rgba(15, 118, 110, 0.05) !important;
            border: 1px solid rgba(15, 118, 110, 0.2) !important;
            border-radius: 8px !important;
            color: #0F766E !important;
            font-weight: 700 !important;
        }

        .flatpickr-time input:hover {
            background: rgba(15, 118, 110, 0.1) !important;
        }

        /* Lenis Smooth Scroll */
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

        /* Creative Animations */
        .reveal-text {
            opacity: 0;
            transform: translateY(30px);
        }

        /* Preloader */
        #preloader {
            transition: opacity 0.5s ease;
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

        /* Innovative Loader Animations */
        @keyframes spin-slow {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spin-reverse {
            to {
                transform: rotate(-360deg);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .animate-spin-slow {
            animation: spin-slow 10s linear infinite;
        }

        .animate-spin-reverse {
            animation: spin-reverse 8s linear infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="font-body bg-slate-50 text-slate-900 antialiased selection:bg-secondary selection:text-white overflow-x-hidden">

    <!-- PRELOADER -->
    <div id="preloader" class="fixed inset-0 z-[100] bg-slate-50 flex flex-col items-center justify-center">
        <!-- Huge Wrapper -->
        <div class="relative w-[300px] h-[300px] flex items-center justify-center mb-12">
            <!-- Decorative Outer Glow -->
            <div class="absolute inset-0 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>

            <!-- Ring 1: Slow Spinning Arc (Outer) -->
            <div
                class="absolute inset-0 border-[6px] border-transparent border-t-primary/20 border-r-primary/20 rounded-full animate-spin-slow">
            </div>

            <!-- Ring 2: Reverse Spinning Arc (Middle) -->
            <div
                class="absolute inset-6 border-[6px] border-transparent border-b-secondary border-l-secondary rounded-full animate-spin-reverse opacity-80">
            </div>

            <!-- Ring 3: Inner Thin Ring -->
            <div class="absolute inset-16 border-[2px] border-slate-200 rounded-full"></div>

            <!-- Center Glowing Orb -->
            <div
                class="relative w-40 h-40 bg-white rounded-full shadow-[0_20px_50px_rgba(15,118,110,0.15)] flex items-center justify-center z-10 animate-pulse-slow ring-4 ring-white">
                <img src="<?php echo base_url('assets/images/favicon.png'); ?>" alt="Loading"
                    class="w-24 h-24 object-contain drop-shadow-sm">
            </div>
        </div>

        <div class="text-center">
            <span
                class="block bg-gradient-to-r from-primary via-teal-500 to-secondary bg-clip-text text-transparent font-heading font-bold text-5xl tracking-[0.4em] uppercase mb-4 drop-shadow-sm">ifyTravels</span>
            <span class="text-slate-400 text-xs tracking-[0.6em] font-medium uppercase opacity-70">Curating Your
                Journey</span>
        </div>
    </div>

    <!-- HIGH-END MODERN HEADER (V7 - CREATIVE SHADOW) -->
    <header id="main-header"
        class="fixed top-0 left-0 right-0 z-50 flex justify-center transition-all duration-300 print:hidden md:top-6">

        <!-- MOBILE APP HEADER -->
        <div
            class="md:hidden w-full bg-slate-50 border-b border-slate-200/60 px-6 py-4 flex justify-between items-center shadow-sm backdrop-blur-md bg-opacity-90">
            <!-- Brand -->
            <a href="<?php echo base_url(); ?>" class="flex items-center gap-2">
                <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels" class="h-8 w-auto">
            </a>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <a href="<?php echo base_url('packages'); ?>"
                    class="p-2 text-slate-500 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>
                <button id="mobile-menu-btn" onclick="openMobileMenu()"
                    class="p-2 text-slate-700 hover:text-primary transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- DESKTOP HEADER CAPSULE -->
        <div id="header-capsule"
            class="hidden md:flex w-[92%] max-w-[1600px] bg-white/80 backdrop-blur-2xl border border-white/60 shadow-creative rounded-3xl px-8 py-5 transition-all duration-500 hover:bg-white ring-1 ring-slate-900/5 hover:shadow-creative-hover items-center justify-between">

            <!-- Logo (Massive) -->
            <a href="<?php echo base_url(); ?>" class="flex items-center gap-2 group px-2" aria-label="ifyTravels Home">
                <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels Logo" width="220"
                    height="65" loading="eager"
                    class="h-14 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
            </a>

            <!-- Desktop Navigation (Grand) -->
            <nav class="flex items-center gap-2 bg-slate-100/50 rounded-full p-1.5 border border-white/50 shadow-inner">
                <?php
                $navLinks = [
                    '' => 'Home',
                    'about' => 'About',
                    'destinations' => 'Destinations',
                    'packages' => 'Packages',
                    'blogs' => 'Blogs',
                    'contact' => 'Contact'
                ];

                foreach ($navLinks as $url => $label):
                    $isActive = (current_url() == base_url($url));
                    ?>
                    <a href="<?php echo base_url($url); ?>"
                        class="relative px-8 py-3 rounded-full text-[16px] font-heading font-bold tracking-wide transition-all duration-300 <?php echo $isActive ? 'bg-white text-slate-900 shadow-md shadow-slate-200' : 'text-slate-600 hover:text-primary hover:bg-white/80'; ?>">
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Auth & Actions (Large) -->
            <div class="flex items-center gap-5 px-2">
                <a href="<?php echo base_url('packages'); ?>"
                    class="flex items-center gap-2 bg-gradient-to-r from-primary to-teal-700 text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5 transition-all duration-300 group">
                    <span>Book Now</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu"
        class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-[60] md:hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
            <span class="font-heading font-bold text-xl text-slate-900">Menu</span>
            <button id="close-menu-btn" onclick="closeMobileMenu()"
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
    <div id="mobile-overlay" onclick="closeMobileMenu()"
        class="fixed inset-0 bg-black/50 opacity-0 invisible transition-all duration-300 z-[55] md:hidden"></div>

    <!-- Scripts -->
    <!-- Critical Mobile Menu Script (No Dependencies) -->
    <script>
        // Use Global Functions for Inline OnClick
        window.openMobileMenu = function () {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileOverlay = document.getElementById('mobile-overlay');
            if (mobileMenu) mobileMenu.classList.remove('translate-x-full');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('invisible', 'opacity-0');
                mobileOverlay.classList.add('visible', 'opacity-100');
            }
            document.body.classList.add('overflow-hidden');
        }

        window.closeMobileMenu = function () {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileOverlay = document.getElementById('mobile-overlay');
            if (mobileMenu) mobileMenu.classList.add('translate-x-full');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('visible', 'opacity-100');
                mobileOverlay.classList.add('invisible', 'opacity-0');
            }
            document.body.classList.remove('overflow-hidden');
        }
    </script>

    <!-- Enhancements Script (GSAP, Lenis) - Safe Loading -->
    <script>
        // Preloader Logic
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            if (preloader && typeof gsap !== 'undefined') {
                gsap.to(preloader, {
                    opacity: 0,
                    duration: 0.8,
                    delay: 0.2,
                    ease: "power2.inOut",
                    onComplete: () => {
                        preloader.style.display = 'none';
                    }
                });
            } else if (preloader) {
                // Fallback if GSAP fails
                preloader.style.opacity = 0;
                setTimeout(() => { preloader.style.display = 'none'; }, 500);
            }
        });

        // Init Smooth Scroll (Lenis) with Safety Check
        if (typeof Lenis !== 'undefined') {
            try {
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
            } catch (e) {
                console.warn("Lenis init failed", e);
            }
        }

        // Header Capsule Animation on Scroll
        const capsule = document.getElementById('header-capsule');
        let isScrolled = false;

        if (capsule) {
            window.addEventListener('scroll', () => {
                const shouldBeScrolled = window.scrollY > 50;

                if (shouldBeScrolled && !isScrolled) {
                    // Shrink
                    capsule.classList.remove('py-5', 'w-[92%]', 'max-w-[1600px]', 'top-6');
                    capsule.classList.add('py-3', 'w-[98%]', 'max-w-full', 'bg-white/95', 'top-2', 'rounded-xl');
                    isScrolled = true;
                } else if (!shouldBeScrolled && isScrolled) {
                    // Expand
                    capsule.classList.add('py-5', 'w-[92%]', 'max-w-[1600px]', 'top-6');
                    capsule.classList.remove('py-3', 'w-[98%]', 'max-w-full', 'bg-white/95', 'top-2', 'rounded-xl');
                    isScrolled = false;
                }
            }, { passive: true });
        }

        // GSAP Animations (Safe)
        document.addEventListener("DOMContentLoaded", (event) => {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                try {
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
                } catch (e) {
                    console.warn("GSAP init failed", e);
                }
            }
        });
    </script>