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

    // Validate Code from DB
    // (Assuming $db is available via functions.php which is required above)
    // We need to use raw PDO or DB helper since header includes functions which includes db
    // Just to be safe, we use the singleton
    try {
        $db = Database::getInstance();
        $aff = $db->fetch("SELECT id FROM affiliates WHERE code = ? AND status = 'active'", [$refCode]);

        if ($aff) {
            $affId = $aff['id'];

            // 1. Set Session
            $_SESSION['affiliate_id'] = $affId;

            // 2. Set Cookie (30 Days)
            setcookie('affiliate_id', $affId, time() + (86400 * 30), "/");

            // 3. Log Referral Click (Anti-Cheat / Analytics)
            try {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $referrer = $_SERVER['HTTP_REFERER'] ?? '';

                // Simple Rate Limit: Check if same IP clicked in last minute for this affiliate
                // Cross-DB compatible: Use PHP calculated timestamp
                $oneMinuteAgo = date('Y-m-d H:i:s', time() - 60);
                $recent = $db->fetch("SELECT id FROM referral_clicks WHERE affiliate_id = ? AND ip_address = ? AND created_at > ?", [$affId, $ip, $oneMinuteAgo]);

                if (!$recent) {
                    $db->execute(
                        "INSERT INTO referral_clicks (affiliate_id, ip_address, user_agent, referrer_url) VALUES (?, ?, ?, ?)",
                        [$affId, $ip, $ua, $referrer]
                    );
                }
            } catch (Exception $e) {
                // Check if table missing (MySQL error 1146)
                if (strpos($e->getMessage(), "doesn't exist") !== false) {
                    try {
                        // Create Table
                        $db->execute("
                            CREATE TABLE IF NOT EXISTS referral_clicks (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                affiliate_id INT NOT NULL,
                                ip_address VARCHAR(45),
                                user_agent TEXT,
                                referrer_url TEXT,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )
                        ");

                        // Retry Insert
                        $db->execute(
                            "INSERT INTO referral_clicks (affiliate_id, ip_address, user_agent, referrer_url) VALUES (?, ?, ?, ?)",
                            [$affId, $ip, $ua, $referrer]
                        );
                    } catch (Exception $ex) {
                        // Still failed? Just ignore.
                        error_log("Referral Table Create Failed: " . $ex->getMessage());
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Ignore DB errors specifically for tracking to avoid blocking page load
        error_log("Affiliate Tracking Error: " . $e->getMessage());
    }
} else {
    // If no ref in URL, check if valid cookie exists but session is empty
    if (!isset($_SESSION['affiliate_id']) && isset($_COOKIE['affiliate_id'])) {
        $cookieAffId = intval($_COOKIE['affiliate_id']);
        // Re-validate against DB to ensure it wasn't tampered or deactivated
        try {
            $db = Database::getInstance();
            $aff = $db->fetch("SELECT id FROM affiliates WHERE id = ? AND status = 'active'", [$cookieAffId]);
            if ($aff) {
                $_SESSION['affiliate_id'] = $aff['id'];
                // Refresh cookie expiry
                setcookie('affiliate_id', $aff['id'], time() + (86400 * 30), "/");
            } else {
                // Invalid/Inactive affiliate in cookie, clear it
                setcookie('affiliate_id', '', time() - 3600, "/");
            }
        } catch (Exception $e) {
            // Ignore
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
    <!-- SEO & Metadata -->
    <!-- Dynamic SEO Variables -->
    <?php
    $metaTitle = isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'ifyTravels') : get_setting('site_name', 'ifyTravels');
    $metaDesc = isset($pageDescription) ? $pageDescription : get_setting('meta_description', 'Discover luxury travel packages and unforgettable destinations with IfyTravels.');

    // Force HTTPS for Canonical/Meta URLs
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "https";
    $metaUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . strtok(strtolower($_SERVER["REQUEST_URI"]), '?'); // Lowercase & Strip query params for canonical
    
    $metaImage = isset($pageImage) ? base_url($pageImage) : (get_setting('og_image') ? base_url(get_setting('og_image')) : base_url('assets/images/logo-color.png'));
    ?>

    <!-- Canonical Tag (Critical for SEO) -->
    <link rel="canonical" href="<?php echo $metaUrl; ?>">

    <!-- Standard SEO -->
    <meta name="description" content="<?php echo e($metaDesc); ?>">
    <meta name="keywords"
        content="<?php echo e(get_setting('meta_keywords', 'travel, tours, holiday packages, destinations, ifytravels')); ?>">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $metaUrl; ?>">
    <meta property="og:title" content="<?php echo e($metaTitle); ?>">
    <meta property="og:description" content="<?php echo e($metaDesc); ?>">
    <meta property="og:image" content="<?php echo $metaImage; ?>">
    <meta property="og:logo" content="<?php echo base_url('assets/images/logo-color.png'); ?>">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="<?php echo $_SERVER['HTTP_HOST']; ?>">
    <meta property="twitter:url" content="<?php echo $metaUrl; ?>">
    <meta name="twitter:title" content="<?php echo e($metaTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($metaDesc); ?>">
    <meta name="twitter:image" content="<?php echo $metaImage; ?>">

    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png?v=' . time()); ?>"
        type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo base_url('assets/images/favicon.png?v=' . time()); ?>">

    <!-- Schema.org Organization for Google Knowledge Graph -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?php echo e(get_setting('site_name', 'ifyTravels')); ?>",
      "url": "<?php echo base_url(); ?>",
      "logo": "<?php echo base_url('assets/images/logo-color.png'); ?>",
      "description": "<?php echo e(get_setting('meta_description', 'Discover luxury travel packages and unforgettable destinations with IfyTravels.')); ?>",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?php echo get_setting('contact_phone', '+919999779870'); ?>",
        "contactType": "customer service"
      },
      "sameAs": [
        "<?php echo get_setting('social_facebook', 'https://facebook.com/ifytravels'); ?>",
        "<?php echo get_setting('social_instagram', 'https://instagram.com/ifytravels'); ?>",
        "<?php echo get_setting('social_twitter', 'https://twitter.com/ifytravels'); ?>",
        "<?php echo get_setting('social_linkedin', 'https://linkedin.com/company/ifytravels'); ?>"
      ]
    }
    </script>

    <!-- Google Analytics -->
    <?php if ($gaId = get_setting('google_analytics_id')): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gaId; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?php echo $gaId; ?>');
        </script>
    <?php endif; ?>

    <!-- Meta Pixel Code -->
    <?php if ($pixelId = get_setting('meta_pixel_id')): ?>
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo $pixelId; ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=<?php echo $pixelId; ?>&ev=PageView&noscript=1" /></noscript>
    <?php endif; ?>

    <!-- Preload Critical Fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"></noscript>

    <!-- Tailwind CSS (Restored to synchronous loading to fix design break) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                        charcoal: '#111827',
                    },
                    fontFamily: { heading: ['Poppins', 'sans-serif'], body: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
        }

        .hero-bg {
            background-image: url('<?php echo base_url('images/hero.png'); ?>');
            background-size: cover;
            background-position: center;
        }

        /* Flatpickr custom styling to match site theme */
        .flatpickr-calendar {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 12px;
        }

        .flatpickr-day.selected {
            background: #0F766E !important;
            border-color: #0F766E !important;
        }

        .flatpickr-day.selected:hover {
            background: #115E5A !important;
        }
    </style>
</head>

<body class="text-charcoal bg-gray-50 flex flex-col min-h-screen">

    <!-- Header -->
    <header
        class="<?php echo (isset($isHome) && $isHome) ? 'fixed w-full z-50 transition-all duration-300 bg-transparent py-4 text-white' : 'fixed w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-md shadow-md py-3 text-charcoal'; ?> transition-header">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="<?php echo base_url(''); ?>"
                class="flex items-center text-2xl font-bold tracking-tight <?php echo (isset($isHome) && $isHome) ? '' : 'text-charcoal'; ?> logo-white-link <?php echo (isset($isHome) && $isHome) ? '' : 'hidden'; ?>">
                <img src="<?php echo base_url('assets/images/logo-white.png?v=' . time()); ?>" alt="ifyTravels Logo White" width="150" height="40">
            </a>
            <a href="<?php echo base_url(); ?>"
                class="flex items-center text-2xl font-bold tracking-tight text-charcoal logo-color-link <?php echo (isset($isHome) && $isHome) ? 'hidden' : ''; ?>">
                <img src="<?php echo base_url('assets/images/logo-color.png?v=' . time()); ?>" alt="ifyTravels Logo Color" width="150" height="40">
            </a>

            <div class="hidden md:flex items-center space-x-8 ml-auto">
                <nav class="flex space-x-8">
                    <a href="<?php echo base_url(''); ?>"
                        class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Home</a>
                    <a href="<?php echo base_url('pages/destinations.php'); ?>"
                        class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Destinations</a>
                    <a href="<?php echo base_url('pages/packages.php'); ?>"
                        class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Packages</a>
                    <a href="<?php echo base_url('pages/contact.php'); ?>"
                        class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Contact</a>
                </nav>

                <div class="flex space-x-4 items-center pl-8 border-l border-gray-200/20">
                    <?php if ($phone = get_setting('contact_phone')): ?>
                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $phone)); ?>"
                            class="hidden lg:flex items-center gap-2 font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <?php echo htmlspecialchars($phone); ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo base_url('pages/booking.php'); ?>"
                        class="bg-white text-primary hover:bg-gray-100 px-6 py-2.5 rounded-full transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-bold text-sm tracking-wide">
                        Book Now
                    </a>
                </div>
            </div>

            <button id="mobile-menu-btn"
                class="md:hidden text-2xl focus:outline-none <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-charcoal'; ?>">
                &#9776;
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-white border-t mt-4 shadow-xl absolute w-full left-0 z-50 p-6 text-charcoal">
            <nav class="flex flex-col space-y-4">
                <a href="<?php echo base_url(''); ?>" class="block font-medium hover:text-primary">Home</a>
                <a href="<?php echo base_url('pages/destinations.php'); ?>"
                    class="block font-medium hover:text-primary">Destinations</a>
                <a href="<?php echo base_url('pages/packages.php'); ?>"
                    class="block font-medium hover:text-primary">Packages</a>
                <a href="<?php echo base_url('pages/contact.php'); ?>"
                    class="block font-medium hover:text-primary">Contact</a>
                <hr class="border-gray-100">
                <?php if ($phone = get_setting('contact_phone')): ?>
                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $phone)); ?>"
                        class="block font-medium text-gray-600 hover:text-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <?php echo htmlspecialchars($phone); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo base_url('pages/booking.php'); ?>"
                    class="block text-center bg-primary text-white px-5 py-3 rounded-lg shadow">Book Now</a>
            </nav>
        </div>
    </header>

    <?php if (isset($isHome) && $isHome): ?>
        <script>
            // Simple scroll effect for Home page to match original JS behavior
            window.addEventListener('scroll', () => {
                const header = document.querySelector('.transition-header');
                const links = header.querySelectorAll('a:not(.bg-primary)'); // Exclude buttons
                const btn = document.getElementById('mobile-menu-btn');
                const logoWhite = document.querySelector('.logo-white');
                const logoColor = document.querySelector('.logo-color');

                if (window.scrollY > 10) {
                    header.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-md', 'py-3', 'text-charcoal');
                    header.classList.remove('bg-transparent', 'py-4', 'text-white');
                    links.forEach(l => { if (!l.classList.contains('text-2xl')) l.classList.replace('text-white', 'text-gray-700'); });
                    if (btn) btn.classList.replace('text-white', 'text-charcoal');

                    if (logoWhite) logoWhite.classList.add('hidden');
                    if (logoColor) logoColor.classList.remove('hidden');
                } else {
                    header.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-md', 'py-3', 'text-charcoal');
                    header.classList.add('bg-transparent', 'py-4', 'text-white');
                    links.forEach(l => { if (!l.classList.contains('text-2xl')) l.classList.replace('text-gray-700', 'text-white'); });
                    if (btn) btn.classList.replace('text-charcoal', 'text-white');

                    if (logoWhite) logoWhite.classList.remove('hidden');
                    if (logoColor) logoColor.classList.add('hidden');
                }
            });

            // Mobile menu toggle
            document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });
        </script>
    <?php else: ?>
        <script>
            // Mobile menu toggle (Non-home)
            document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });
        </script>
        <div class="h-20"></div> <!-- Spacer for fixed header on non-home pages -->
    <?php endif; ?>