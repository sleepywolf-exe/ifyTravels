<?php
// Main Home Controller

// Main Home Controller
require_once 'includes/functions.php';

$pageTitle = "Experience the Extraordinary";
$isHome = true;
include 'includes/header.php';
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "<?php echo get_setting('site_name', 'ifyTravels'); ?>",
  "url": "<?php echo base_url(); ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?php echo base_url('packages?search={search_term_string}'); ?>",
    "query-input": "required name=search_term_string"
  }
}
</script>

<?php
// Data Logic
$destinations = $destinations ?? [];
$featured = array_filter($destinations, fn($d) => !empty($d['is_featured']));
$others = array_filter($destinations, fn($d) => empty($d['is_featured']));
$topDestinations = array_merge($featured, $others);
$topDestinations = array_slice($topDestinations, 0, 4);

// FALLBACK: Mock Data if DB is empty related to responsive check
if (empty($topDestinations)) {
    $topDestinations = [
        [
            'name' => 'Maldives',
            'slug' => 'maldives',
            'image' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800&q=80',
            'rating' => '4.9',
            'is_featured' => 1
        ],
        [
            'name' => 'Switzerland',
            'slug' => 'switzerland',
            'image' => 'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?w=800&q=80',
            'rating' => '5.0',
            'is_featured' => 1
        ],
        [
            'name' => 'Dubai',
            'slug' => 'dubai',
            'image' => 'https://images.unsplash.com/photo-1512453979798-5ea904ac6666?w=800&q=80',
            'rating' => '4.8',
            'is_featured' => 0
        ],
        [
            'name' => 'Bali',
            'slug' => 'bali',
            'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80',
            'rating' => '4.7',
            'is_featured' => 0
        ]
    ];
}

$packages = $packages ?? [];
$popularPackages = array_filter($packages, fn($p) => $p['isPopular']);
if (empty($popularPackages))
    $popularPackages = array_slice($packages, 0, 3);
$popularPackages = array_slice($popularPackages, 0, 3);

// Load Testimonials
$testimonials = loadTestimonials(3);

// Count Stats for Trust Bar
$stats = [
    'trips' => 500, // Base
    'reviews' => 98, // Percentage
    'destinations' => 50,
];

try {
    $db = Database::getInstance();

    // Trips: count bookings + base
    $bookingCount = $db->fetch("SELECT COUNT(*) as c FROM bookings");
    $stats['trips'] = 500 + ($bookingCount['c'] ?? 0);

    // Reviews: count 5-star testimonials
    $reviewCount = $db->fetch("SELECT COUNT(*) as c FROM testimonials WHERE rating = 5");
    // Calculate simple percentage or keep static 98% for now if low volume

    // Destinations: count active
    $destCount = $db->fetch("SELECT COUNT(*) as c FROM destinations");
    if ($destCount && $destCount['c'] > 0) {
        $stats['destinations'] = $destCount['c'];
    }
} catch (Exception $e) {
    // Silent fail, use defaults
}
?>

<!-- Fix for White Stripe & Mobile Height -->
<style>
    body {
        background-color: #0f172a !important;
        /* Match hero dark theme to hide leaks */
    }
</style>

<!-- PARALLAX BACKGROUND (Fixed) -->
<div class="fixed inset-0 z-0 bg-slate-900">
    <img src="<?php echo get_setting('hero_bg', base_url('assets/images/destinations/bali.png')); ?>" alt="Bali Luxury"
        class="w-full h-full object-cover object-[center_30%] parallax-bg brightness-[0.60]">
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-900/60"></div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<main class="relative z-10">

    <!-- HERO SECTION -->
    <section
        class="min-h-[100dvh] md:h-[100dvh] flex flex-col items-center justify-center overflow-hidden relative pt-20 pb-20 md:py-0">
        <div class="container mx-auto px-4 text-center z-20 relative mt-10 md:mt-32">
            <!-- Animated Hero Title -->
            <h1
                class="hero-title opacity-0 transform translate-y-10 will-change-transform text-4xl sm:text-6xl md:text-9xl font-bold mb-6 md:mb-10 font-heading tracking-tight leading-none text-white drop-shadow-2xl">
                <?php echo get_setting('hero_title', 'Experience the <br /> <span class="text-white italic pr-2 font-serif">Extraordinary</span>'); ?>
            </h1>

            <div
                class="hero-subtitle opacity-0 transform translate-y-10 will-change-transform mb-8 md:mb-12 max-w-3xl mx-auto">
                <div
                    class="inline-block bg-black/20 backdrop-blur-md border border-white/10 rounded-2xl p-4 md:p-6 shadow-2xl">
                    <div
                        class="text-base md:text-2xl text-white font-light leading-relaxed tracking-wide drop-shadow-md [&>p]:mb-2 [&>p:last-child]:mb-0">
                        <?php echo get_setting('hero_subtitle', "Curated luxury travel experiences designed just for you.<br>📞 +91 9999779870 | 📧 hello@ifytravel.com"); ?>
                    </div>
                </div>
            </div>

            <!-- Glass Booking Form -->
            <div class="hero-form opacity-0 transform translate-y-10 max-w-5xl mx-auto w-full relative group">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-white/40 to-white/20 rounded-3xl md:rounded-full blur opacity-30 group-hover:opacity-50 transition duration-1000">
                </div>
                <!-- Search Bar Container -->
                <div
                    class="glass-form p-2 rounded-3xl md:rounded-full relative bg-white/10 border border-white/20 backdrop-blur-xl shadow-creative">
                    <form action="<?php echo base_url('packages'); ?>" method="GET"
                        class="flex flex-col md:flex-row gap-2">

                        <!-- Destination -->
                        <div class="relative flex-1 group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-white/80 group-hover/input:text-white transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <select name="destination"
                                class="w-full pl-12 pr-4 py-3 md:py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none appearance-none cursor-pointer transition-all text-sm md:text-base">
                                <option value="" class="text-slate-900 bg-white">Where to go?</option>
                                <?php foreach ($destinations as $dest): ?>
                                    <option value="<?php echo $dest['id']; ?>" class="text-slate-900 bg-white">
                                        <?php echo htmlspecialchars($dest['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="relative flex-1 group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-white/80 group-hover/input:text-white transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <input type="text" id="departure-date" name="date"
                                class="w-full pl-12 pr-4 py-3 md:py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none transition-all text-sm md:text-base"
                                placeholder="When?">
                        </div>

                        <!-- Travelers -->
                        <div class="relative w-full md:w-32 group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-white/80 group-hover/input:text-white transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <input type="number" name="travelers" min="1"
                                class="w-full pl-12 pr-4 py-3 md:py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none transition-all text-sm md:text-base"
                                placeholder="Guests">
                        </div>

                        <!-- Button -->
                        <button type="submit"
                            class="bg-white text-primary hover:bg-slate-50 font-bold py-3 md:py-4 px-10 rounded-full shadow-creative hover:shadow-creative-hover transform hover:scale-105 transition duration-300 magnetic-btn whitespace-nowrap text-sm md:text-base relative z-10">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator - Hidden on very small screens to avoid overlap -->
        <div
            class="absolute bottom-6 md:bottom-12 left-1/2 transform -translate-x-1/2 animate-bounce z-20 hidden sm:block">
            <a href="#destinations" aria-label="Scroll Down"
                class="text-white hover:text-primary transition-colors duration-300">
                <svg class="w-8 h-8 md:w-10 md:h-10 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7-7-7m7 7V3">
                    </path>
                </svg>
            </a>
        </div>
        </div>
    </section>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="relative z-10 bg-white">

        <!-- LIVE STATS BAR (Trust Signals) -->
        <div class="relative mt-0 mb-20 z-30">
            <div class="container mx-auto px-4">
                <div
                    class="bg-white rounded-2xl shadow-creative border border-slate-100 p-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center bg-white/90 backdrop-blur-md">
                    <!-- Stat 1 -->
                    <div class="flex items-center justify-center gap-5 group p-2">
                        <div class="text-primary/20 group-hover:text-primary transition-colors duration-300">
                            <i class="fa-solid fa-plane-departure text-4xl md:text-5xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-4xl md:text-5xl font-bold text-primary leading-none mb-1">
                                <?php echo get_setting('stat_trips') ?: '500+'; ?>
                            </p>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold">Luxury Trips</p>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="flex items-center justify-center gap-5 group p-2">
                        <div class="text-primary/20 group-hover:text-primary transition-colors duration-300">
                            <i class="fa-solid fa-star text-4xl md:text-5xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-4xl md:text-5xl font-bold text-primary leading-none mb-1">
                                <?php echo get_setting('stat_reviews') ?: '98%'; ?>
                            </p>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold">5-Star Reviews</p>
                        </div>
                    </div>

                    <!-- Divider for mobile -->
                    <div class="col-span-2 border-t border-slate-100 md:hidden my-2"></div>

                    <!-- Stat 3 -->
                    <div class="flex items-center justify-center gap-5 group p-2">
                        <div class="text-primary/20 group-hover:text-primary transition-colors duration-300">
                            <i class="fa-solid fa-map-location-dot text-4xl md:text-5xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-4xl md:text-5xl font-bold text-primary leading-none mb-1">
                                <?php echo get_setting('stat_destinations') ?: '25+'; ?>
                            </p>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold">Destinations</p>
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div class="flex items-center justify-center gap-5 group p-2">
                        <div class="text-primary/20 group-hover:text-primary transition-colors duration-300">
                            <i class="fa-solid fa-headset text-4xl md:text-5xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-4xl md:text-5xl font-bold text-primary leading-none mb-1">
                                <?php echo get_setting('stat_concierge') ?: '24/7'; ?>
                            </p>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold">Concierge</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESTINATIONS (Redesigned) -->
        <section id="destinations" class="py-32 relative overflow-hidden bg-white">
            <!-- Massive Background Text -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
                <h2
                    class="text-[12rem] md:text-[24rem] font-black text-slate-900 opacity-[0.03] leading-none tracking-tighter uppercase font-heading transform -translate-y-20">
                    Discover
                </h2>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <!-- Section Header (Floating) -->
                <div class="text-center mb-16 section-header opacity-0 transform translate-y-10">
                    <span
                        class="inline-block py-2 px-5 rounded-full bg-primary/10 text-primary font-bold tracking-widest uppercase text-xs mb-4">
                        Explore the World
                    </span>
                    <h3 class="text-4xl md:text-6xl font-heading font-extrabold text-slate-900">
                        Trending <span class="text-primary italic font-serif">Destinations</span>
                    </h3>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-2">
                    <?php foreach ($topDestinations as $index => $dest): ?>
                        <div class="destination-card opacity-0">
                            <a href="<?php echo destination_url($dest['slug']); ?>"
                                class="block group relative h-[550px] rounded-[2.5rem] overflow-hidden shadow-creative hover:shadow-creative-hover transition-all duration-500 border-[6px] border-white ring-1 ring-slate-100">

                                <!-- Image -->
                                <img src="<?php echo base_url($dest['image']); ?>"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                    loading="lazy" alt="<?php echo htmlspecialchars($dest['name']); ?>">

                                <!-- Gradient Overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-500">
                                </div>

                                <!-- Content -->
                                <div class="absolute bottom-0 left-0 w-full p-8 flex flex-col justify-end h-full">
                                    <div
                                        class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                        <h3 class="text-4xl font-heading font-black text-white mb-2 leading-tight">
                                            <?php echo htmlspecialchars($dest['name']); ?>
                                        </h3>

                                        <div class="flex items-end justify-between w-full">
                                            <div
                                                class="flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                                                <span class="text-yellow-400 text-lg">★</span>
                                                <span
                                                    class="text-white font-bold tracking-wide"><?php echo $dest['rating']; ?></span>
                                            </div>

                                            <span
                                                class="w-14 h-14 rounded-full bg-white flex items-center justify-center text-primary shadow-lg transform translate-y-2 group-hover:translate-y-0 group-hover:rotate-45 transition-all duration-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-20 relative z-10">
                    <a href="<?php echo base_url('/destinations'); ?>"
                        class="inline-flex items-center gap-3 px-10 py-5 bg-slate-900 text-white rounded-full font-bold hover:bg-primary transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-1 magnetic-btn group">
                        <span>View All Destinations</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- WHY CHOOSE US (Features) -->
        <section class="py-32 bg-white relative overflow-hidden border-y border-slate-100">
            <!-- Massive Background Text -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
                <h2
                    class="text-[12rem] md:text-[20rem] font-black text-slate-900 opacity-[0.03] leading-none tracking-tighter uppercase font-heading transform -translate-y-20">
                    Experience
                </h2>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16 section-header opacity-0">
                    <span
                        class="inline-block py-2 px-5 rounded-full bg-primary/10 text-primary font-bold tracking-widest uppercase text-xs mb-4">
                        The Difference
                    </span>
                    <h2 class="text-4xl md:text-6xl font-heading font-extrabold text-slate-900 mt-2">
                        Why Choose <span class="text-primary italic font-serif">IfyTravels</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="feature-card depth-card p-10 rounded-[2rem] bg-slate-50 border border-slate-100 ring-1 ring-slate-200/50 group opacity-0 translate-y-10">
                        <div
                            class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center mb-8 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4 font-heading">Curated Luxury</h3>
                        <p class="text-slate-600 leading-relaxed text-lg">Every destination is hand-picked by our
                            experts to
                            ensure specific standards of luxury and comfort.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card p-10 rounded-[2rem] bg-slate-50 border border-slate-100 ring-1 ring-slate-200/50 hover:shadow-xl transition-all duration-300 group opacity-0 translate-y-10 hover:-translate-y-2"
                        style="transition-delay: 100ms;">
                        <div
                            class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mb-8 text-secondary group-hover:bg-secondary group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4 font-heading">24/7 Concierge</h3>
                        <p class="text-slate-600 leading-relaxed text-lg">Our dedicated support team is available
                            round-the-clock to assist you with any request.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card p-10 rounded-[2rem] bg-slate-50 border border-slate-100 ring-1 ring-slate-200/50 hover:shadow-xl transition-all duration-300 group opacity-0 translate-y-10 hover:-translate-y-2"
                        style="transition-delay: 200ms;">
                        <div
                            class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mb-8 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4 font-heading">Best Price Guarantee</h3>
                        <p class="text-slate-600 leading-relaxed text-lg">We partner directly with resorts and airlines
                            to bring
                            you exclusive rates you won't find elsewhere.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PACKAGES (Featured) - Redesigned -->
        <?php if (!empty($popularPackages)): ?>
            <section class="py-32 bg-slate-50 relative overflow-hidden">
                <!-- Massive Background Text -->
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
                    <h2
                        class="text-[12rem] md:text-[20rem] font-black text-slate-900 opacity-[0.03] leading-none tracking-tighter uppercase font-heading transform -translate-y-20">
                        Exclusive
                    </h2>
                </div>

                <div class="container mx-auto px-6 relative z-10">
                    <div class="text-center mb-16 section-header opacity-0 transform translate-y-10">
                        <span
                            class="inline-block py-2 px-5 rounded-full bg-secondary/10 text-secondary font-bold tracking-widest uppercase text-xs mb-4">
                            Handpicked for You
                        </span>
                        <h2 class="text-4xl md:text-6xl font-heading font-extrabold text-slate-900">
                            Popular <span class="text-secondary italic font-serif">Packages</span>
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <?php foreach ($popularPackages as $index => $pkg): ?>
                            <div class="package-card opacity-0">
                                <a href="<?php echo package_url($pkg['slug']); ?>"
                                    class="block group relative h-[500px] rounded-[2.5rem] overflow-hidden shadow-2xl hover:shadow-[0_20px_50px_rgba(244,63,94,0.3)] transition-all duration-500 border-[6px] border-white ring-1 ring-slate-100">

                                    <!-- Image -->
                                    <img src="<?php echo base_url($pkg['image']); ?>"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                        loading="lazy" alt="<?php echo htmlspecialchars($pkg['title']); ?>">

                                    <!-- Gradient Overlay -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-500">
                                    </div>

                                    <!-- Badge -->
                                    <div class="absolute top-6 left-6 z-20">
                                        <span
                                            class="px-4 py-2 bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full uppercase tracking-wider">
                                            <?php echo htmlspecialchars($pkg['duration']); ?>
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <div class="absolute bottom-0 left-0 w-full p-8 flex flex-col justify-end h-full">
                                        <div
                                            class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                            <h3 class="text-3xl font-heading font-black text-white mb-2 leading-tight">
                                                <?php echo htmlspecialchars($pkg['title']); ?>
                                            </h3>

                                            <div class="flex items-end justify-between w-full mt-4">
                                                <div>
                                                    <p class="text-slate-300 text-sm font-medium uppercase tracking-wider mb-1">
                                                        Starting from</p>
                                                    <p class="text-white text-2xl font-bold">
                                                        ₹<?php echo number_format($pkg['price']); ?>
                                                    </p>
                                                </div>

                                                <span
                                                    class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center shadow-lg transform translate-y-2 group-hover:translate-y-0 group-hover:rotate-45 transition-all duration-500">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-16">
                        <a href="<?php echo base_url('/packages'); ?>"
                            class="inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 border border-slate-200 rounded-full font-bold hover:bg-secondary hover:text-white hover:border-secondary transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-1 magnetic-btn group">
                            <span>View All Offers</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>



        <!-- TESTIMONIALS (Restored) -->
        <section class="py-32 bg-slate-50 border-t border-slate-200 relative overflow-hidden">
            <!-- Massive Background Text -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
                <h2
                    class="text-[12rem] md:text-[22rem] font-black text-slate-200 opacity-[0.2] leading-none tracking-tighter uppercase font-heading transform -translate-y-20">
                    Stories
                </h2>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-20 section-header opacity-0">
                    <span
                        class="inline-block py-2 px-5 rounded-full bg-primary/10 text-primary font-bold tracking-widest uppercase text-xs mb-4">Testimonials</span>
                    <h2 class="text-4xl md:text-6xl font-heading font-extrabold text-slate-900 mt-2">Loved by <span
                            class="text-primary italic font-serif">Travelers</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $index => $review): ?>
                            <div class="testimonial-card p-10 bg-white rounded-[2rem] shadow-xl border border-slate-100 opacity-0 translate-y-10 hover:shadow-2xl transition-all duration-300"
                                style="transition-delay: <?php echo $index * 150; ?>ms">
                                <div class="flex items-center gap-5 mb-8">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['name']); ?>&background=random"
                                        alt="<?php echo htmlspecialchars($review['name']); ?>"
                                        class="w-16 h-16 rounded-full ring-4 ring-slate-50 shadow-md">
                                    <div>
                                        <h4 class="font-bold text-xl text-slate-900">
                                            <?php echo htmlspecialchars($review['name']); ?>
                                        </h4>
                                        <?php if (!empty($review['location'])): ?>
                                            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">
                                                <?php echo htmlspecialchars($review['location']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="flex text-yellow-500 text-base mt-1">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <span><?php echo ($i < $review['rating']) ? '★' : '☆'; ?></span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-slate-600 italic leading-loose text-lg font-light">
                                    "<?php echo htmlspecialchars($review['message']); ?>"</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-10 opacity-70">
                            <p class="text-xl text-slate-400 italic font-light">"No reviews yet. Be the first to share your
                                journey!"</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- LUXURY CTA SECTION -->
        <section class="py-40 relative overflow-hidden bg-slate-900">
            <!-- Massive Background Text -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full text-center pointer-events-none select-none z-0">
                <h2
                    class="text-[12rem] md:text-[25rem] font-black text-white opacity-[0.02] leading-none tracking-tighter uppercase font-heading">
                    Journey
                </h2>
            </div>

            <!-- Innovative Background Effects (New) -->
            <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
                <!-- 1. Animated Flight Paths -->
                <svg class="absolute inset-0 w-full h-full opacity-40" viewBox="0 0 1440 800" fill="none"
                    preserveAspectRatio="none">
                    <path class="flight-path" d="M-100,600 C300,400 600,800 1540,200" stroke="url(#grad1)"
                        stroke-width="2" stroke-dasharray="10 10" />
                    <path class="flight-path" d="M-100,200 C400,500 900,100 1540,600" stroke="url(#grad2)"
                        stroke-width="2" stroke-dasharray="15 15" />
                    <path class="flight-path" d="M-100,400 C300,100 1100,700 1540,300" stroke="url(#grad1)"
                        stroke-width="1" stroke-dasharray="8 8" opacity="0.5" />

                    <defs>
                        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="transparent" />
                            <stop offset="50%" stop-color="#0F766E" />
                            <stop offset="100%" stop-color="transparent" />
                        </linearGradient>
                        <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="transparent" />
                            <stop offset="50%" stop-color="#D97706" />
                            <stop offset="100%" stop-color="transparent" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- 2. Floating Destination Portals (Glass Shards) -->
                <!-- Portal 1 -->
                <div
                    class="floating-portal absolute top-20 left-[10%] w-32 h-40 bg-white/5 rounded-2xl rotate-[-6deg] border border-white/10 backdrop-blur-md overflow-hidden opacity-30">
                    <img src="https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?auto=format&fit=crop&q=80&w=200"
                        class="w-full h-full object-cover opacity-60 mix-blend-overlay">
                </div>
                <!-- Portal 2 -->
                <div
                    class="floating-portal absolute bottom-32 right-[15%] w-40 h-28 bg-white/5 rounded-2xl rotate-[12deg] border border-white/10 backdrop-blur-md overflow-hidden opacity-30">
                    <img src="https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?auto=format&fit=crop&q=80&w=200"
                        class="w-full h-full object-cover opacity-60 mix-blend-overlay">
                </div>
                <!-- Portal 3 -->
                <div
                    class="floating-portal absolute top-1/3 right-[5%] w-24 h-24 rounded-full bg-gradient-to-tr from-primary/20 to-secondary/20 border border-white/10 backdrop-blur-xl opacity-40 blur-sm">
                </div>
            </div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="max-w-5xl mx-auto">
                    <span
                        class="inline-block py-2 px-6 rounded-full bg-white/10 border border-white/10 text-white text-sm font-bold tracking-[0.2em] mb-8 uppercase backdrop-blur-sm">
                        Start Your Journey
                    </span>
                    <h2 class="text-6xl md:text-8xl font-heading font-black mb-10 text-white leading-tight reveal-text">
                        Ready to Explore the <br /><span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Extraordinary?</span>
                    </h2>
                    <p
                        class="text-3xl md:text-5xl text-slate-200 mb-16 font-light max-w-4xl mx-auto leading-tight drop-shadow-lg">
                        Join the elite travelers who have discovered the world's most breathtaking destinations with
                        ifyTravels.
                    </p>
                    <div class="flex flex-col md:flex-row gap-8 justify-center items-center">
                        <a href="<?php echo base_url('pages/booking.php'); ?>"
                            class="bg-primary text-white font-bold py-6 px-16 rounded-full shadow-[0_20px_50px_rgba(15,118,110,0.5)] hover:shadow-[0_20px_50px_rgba(15,118,110,0.7)] transform hover:-translate-y-1 transition-all duration-300 text-xl magnetic-btn border border-primary ring-4 ring-primary/20">
                            Book Your Trip
                        </a>
                        <a href="<?php echo base_url('pages/contact.php'); ?>"
                            class="bg-transparent border-2 border-white/20 text-white font-bold py-6 px-16 rounded-full hover:bg-white hover:text-slate-900 transition-all duration-300 text-xl magnetic-btn hover:shadow-2xl">
                            Contact Concierge
                        </a>
                    </div>
                </div>
            </div>
            <!-- Glow Effects -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] bg-primary/20 rounded-full blur-[150px] pointer-events-none mix-blend-screen">
            </div>
        </section>

        <!-- NEWSLETTER SECTION -->
        <section class="py-32 relative overflow-hidden bg-slate-900 border-t border-slate-800">
            <!-- Massive Background Text -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full text-center pointer-events-none select-none z-0">
                <h2
                    class="text-[10rem] md:text-[20rem] font-black text-white opacity-[0.02] leading-none tracking-tighter uppercase font-heading">
                    Subscribe
                </h2>
            </div>

            <div
                class="absolute inset-0 bg-gradient-to-r from-primary/10 to-secondary/10 opacity-30 pointer-events-none">
            </div>
            <!-- Pattern -->
            <div class="absolute inset-0 opacity-5 pointer-events-none"
                style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <span
                    class="inline-block py-2 px-6 rounded-full bg-white/5 border border-white/10 text-white text-xs font-bold tracking-[0.2em] mb-8 uppercase backdrop-blur-sm">
                    Stay Connected
                </span>
                <h2 class="text-4xl md:text-6xl font-heading font-black text-white mb-6">
                    <?php echo e(get_setting('newsletter_heading', 'Join the Elite Club')); ?>
                </h2>
                <p class="text-slate-400 mb-12 max-w-2xl mx-auto text-lg font-light leading-relaxed">
                    <?php echo e(get_setting('newsletter_text', 'Subscribe to receive exclusive offers, travel inspiration, and member-only perks directly to your inbox.')); ?>
                </p>

                <form id="newsletter-form" class="max-w-lg mx-auto flex flex-col sm:flex-row gap-4 relative">
                    <div class="relative flex-1">
                        <input type="email" name="email" placeholder="Your email address"
                            class="w-full px-8 py-5 rounded-full bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:bg-white/10 focus:border-primary/50 transition-all backdrop-blur-md"
                            required>
                    </div>
                    <button type="submit"
                        class="px-10 py-5 rounded-full bg-white text-slate-900 font-bold hover:bg-primary hover:text-white transition-all duration-300 shadow-[0_10px_30px_rgba(0,0,0,0.2)] hover:shadow-[0_20px_40px_rgba(20,184,166,0.4)] transform hover:-translate-y-1 magnetic-btn">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>

        <script>
            // Newsletter Handler
            document.getElementById('newsletter-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;
                const btn = form.querySelector('button');
                const originalText = btn.innerText;

                btn.innerText = 'Joining...';
                btn.disabled = true;

                const formData = new FormData(form);

                fetch('<?php echo base_url("services/subscribe.php"); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            btn.innerText = 'Joined! ✓';
                            btn.classList.add('!bg-green-500', '!text-white');
                            form.reset();
                            setTimeout(() => {
                                btn.innerText = originalText;
                                btn.disabled = false;
                                btn.classList.remove('!bg-green-500', '!text-white');
                            }, 3000);
                        } else {
                            alert(data.message);
                            btn.innerText = originalText;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.innerText = originalText;
                        btn.disabled = false;
                        alert('Something went wrong. Please try again.');
                    });
            });
        </script>

        <!-- GSAP Animation Logic -->
        <!-- GSAP Animation Logic (Premium) -->
        <script>
            document.addEventListener("DOMContentLoaded", (event) => {
                gsap.registerPlugin(ScrollTrigger);

                // Helper for Staggered Reveals
                const animateBatch = (selector, yOffset = 100) => {
                    ScrollTrigger.batch(selector, {
                        start: "top 90%",
                        onEnter: batch => {
                            gsap.fromTo(batch,
                                { opacity: 0, y: yOffset, scale: 0.95 },
                                {
                                    opacity: 1,
                                    y: 0,
                                    scale: 1,
                                    stagger: 0.15,
                                    duration: 1.2,
                                    ease: "power4.out",
                                    overwrite: true
                                }
                            );
                        },
                        once: true
                    });
                };

                // 1. Hero Text Reveal (Load Animation)
                const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
                tl.to(".hero-title", { y: 0, opacity: 1, duration: 1.5, delay: 0.2 })
                    .to(".hero-subtitle", { y: 0, opacity: 1, duration: 1.2 }, "-=1")
                    .to(".hero-form", { y: 0, opacity: 1, duration: 1.2 }, "-=0.8");

                // 2. Section Headers (Reveal Up)
                gsap.utils.toArray('.section-header').forEach(header => {
                    gsap.fromTo(header,
                        { opacity: 0, y: 50 },
                        {
                            scrollTrigger: {
                                trigger: header,
                                start: "top 85%",
                                toggleActions: "play none none reverse"
                            },
                            opacity: 1,
                            y: 0,
                            duration: 1,
                            ease: "power3.out"
                        }
                    );
                });

                // 3. Premium Card Reveals (Batch)
                animateBatch('.destination-card', 100);
                animateBatch('.feature-card', 60);
                animateBatch('.package-card', 100);
                animateBatch('.testimonial-card', 60);

                // 5. Flight Path Animation (Innovative)
                gsap.utils.toArray('.flight-path').forEach((path, i) => {
                    const length = path.getTotalLength();
                    path.style.strokeDasharray = length;
                    path.style.strokeDashoffset = length;

                    gsap.to(path, {
                        strokeDashoffset: 0,
                        duration: 3 + i,
                        ease: "power1.inOut",
                        repeat: -1,
                        yoyo: true,
                        scrollTrigger: {
                            trigger: ".flight-path",
                            start: "top bottom",
                            toggleActions: "play pause resume pause"
                        }
                    });
                });

                // 6. Floating Portals (Levitation)
                gsap.to(".floating-portal", {
                    y: -20,
                    rotation: 5,
                    duration: 3,
                    stagger: {
                        each: 0.5,
                        yoyo: true,
                        repeat: -1
                    },
                    ease: "sine.inOut"
                });

                // 4. Newsletter Reveal
                gsap.fromTo("#newsletter-form",
                    { opacity: 0, scale: 0.9 },
                    {
                        scrollTrigger: {
                            trigger: "#newsletter-form",
                            start: "top 90%"
                        },
                        opacity: 1,
                        scale: 1,
                        duration: 1,
                        ease: "elastic.out(1, 0.6)"
                    }
                );
            });
        </script>

        <!-- Flatpickr Script (Deferred) -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr("#departure-date", {
                    minDate: "today",
                    dateFormat: "d-M-Y",
                    altInput: true,
                    altFormat: "d M, Y",
                    disableMobile: "true"
                });
            });
        </script>

    </div><!-- End Content Wrapper -->

    <?php include 'includes/footer.php'; ?>