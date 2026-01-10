<?php
// Ensure functions and data are loaded
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../data/loader.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'ifyTravels') : get_setting('site_name', 'ifyTravels'); ?>
    </title>
    <!-- SEO & Metadata -->
    <meta name="description"
        content="<?php echo e(get_setting('meta_description', 'Discover luxury travel packages and unforgettable destinations with IfyTravels.')); ?>">
    <meta name="keywords"
        content="<?php echo e(get_setting('meta_keywords', 'travel, tours, holiday packages, destinations, ifytravels')); ?>">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="<?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title"
        content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'IfyTravels') : get_setting('site_name', 'IfyTravels'); ?>">
    <meta property="og:description"
        content="<?php echo e(get_setting('meta_description', 'Discover luxury travel packages and unforgettable destinations with IfyTravels.')); ?>">
    <?php if ($ogImage = get_setting('og_image')): ?>
        <meta property="og:image" content="<?php echo base_url($ogImage); ?>">
    <?php endif; ?>

    <?php
    $favicon = get_setting('site_favicon');
    if (!$favicon) {
        $favicon = get_setting('site_logo');
    }
    if ($favicon): ?>
        <link rel="shortcut icon" href="<?php echo base_url($favicon); ?>" type="image/x-icon">
    <?php endif; ?>

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

    <!-- Tailwind CSS (CDN for simplicity as per original, or local build) -->
    <!-- We valid link to output.css if built, or CDN as fallback/dev -->
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
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Flatpickr CSS for modern date picker -->
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
                class="flex items-center text-2xl font-bold tracking-tight <?php echo (isset($isHome) && $isHome) ? '' : 'text-charcoal'; ?>">

                <img src="<?php echo base_url('assets/images/logo-white.png?v=' . time()); ?>"
                    alt="<?php echo e(get_setting('site_name', 'ifyTravels')); ?>"
                    class="h-12 object-contain logo-white <?php echo (isset($isHome) && $isHome) ? '' : 'hidden'; ?>">

                <img src="<?php echo base_url('assets/images/logo-color.png?v=' . time()); ?>"
                    alt="<?php echo e(get_setting('site_name', 'ifyTravels')); ?>"
                    class="h-12 object-contain logo-color <?php echo (isset($isHome) && $isHome) ? 'hidden' : ''; ?>">
            </a>

            <nav class="hidden md:flex space-x-8">
                <a href="<?php echo base_url(''); ?>"
                    class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Home</a>
                <a href="<?php echo base_url('pages/destinations.php'); ?>"
                    class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Destinations</a>
                <a href="<?php echo base_url('pages/packages.php'); ?>"
                    class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Packages</a>
                <a href="<?php echo base_url('pages/contact.php'); ?>"
                    class="font-medium hover:text-primary transition <?php echo (isset($isHome) && $isHome) ? 'text-white' : 'text-gray-700'; ?>">Contact</a>
            </nav>

            <div class="hidden md:flex space-x-4 items-center">
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
                    class="bg-primary text-white px-6 py-2 rounded-full hover:bg-blue-700 transition shadow hover:shadow-lg transform hover:-translate-y-0.5 font-medium">Book
                    Now</a>
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