<?php
// includes/mobile_header.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>
        <?php echo $pageTitle ?? get_setting('site_name', 'ifyTravels'); ?>
    </title>

    <!-- FORCE DESKTOP VIEW for Large Screens -->
    <script>
        (function () {
            // If screen is desktop-sized (> 1024px) but we are serving Mobile view,
            // STRICTLY redirect to Main Desktop Site.
            if (window.innerWidth > 1024) {
                window.location.href = "<?php echo base_url(''); ?>";
            }
        })();
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'], // Keeping Outfit for brand
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700;900&display=swap"
        rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        /* Mobile Specific Overrides */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            /* Slate-50 */
            -webkit-tap-highlight-color: transparent;
            padding-bottom: env(safe-area-inset-bottom);
            overflow-x: hidden;
            /* Prevent horizontal scroll from Swipers */
        }

        .pb-safe {
            padding-bottom: calc(80px + env(safe-area-inset-bottom));
        }

        .pt-safe {
            padding-top: env(safe-area-inset-top);
        }

        /* Hide scrollbar for horizontal containers */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Disable text selection for app-like feel */
        .select-none {
            user-select: none;
            -webkit-user-select: none;
        }
    </style>
    <!-- Global Mobile CSS -->
    <style>
        /* Safe Area Support */
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Hide Scrollbar but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Remove Tap Highlight */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-primary/20 selection:text-primary pb-24">

    <!-- APP HEADER (Sticky) -->
    <header
        class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 pt-safe transition-all duration-300">
        <div class="px-4 h-16 flex items-center justify-between">

            <?php
            $current_script = basename($_SERVER['PHP_SELF']);
            $root_pages = ['index.php', 'explore.php', 'saved.php', 'profile.php'];
            $is_root = in_array($current_script, $root_pages);
            ?>

            <!-- Left Side: Back Btn OR Brand -->
            <div class="flex items-center gap-2">
                <?php if (!$is_root): ?>
                    <button onclick="history.back()"
                        class="p-2 -ml-2 text-slate-600 rounded-full hover:bg-slate-100 active:scale-95">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <span class="font-heading font-bold text-lg text-slate-900">
                        <?php echo $pageTitle ?? 'Back'; ?>
                    </span>
                <?php else: ?>
                    <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" class="h-6 w-auto" alt="ifyTravels">
                <?php endif; ?>
            </div>

            <!-- Actions (Only on Root Pages) -->
            <?php if ($is_root): ?>
                <div class="flex items-center gap-3">
                    <a href="<?php echo base_url('mobile/notifications.php'); ?>"
                        class="relative p-2 text-slate-600 rounded-full hover:bg-slate-100 active:scale-95 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Dot -->
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </a>

                    <a href="<?php echo base_url('mobile/search.php'); ?>"
                        class="p-2 text-slate-600 rounded-full hover:bg-slate-100 active:scale-95 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>