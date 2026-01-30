<?php
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

$packages = $packages ?? [];
$popularPackages = array_filter($packages, fn($p) => $p['isPopular']);
if (empty($popularPackages))
    $popularPackages = array_slice($packages, 0, 3);
$popularPackages = array_slice($popularPackages, 0, 3);

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

<!-- PARALLAX BACKGROUND (Fixed) -->
<div class="fixed inset-0 z-0">
    <img src="<?php echo get_setting('hero_bg', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&q=80&w=2000'); ?>"
        alt="Maldives Luxury" class="w-full h-full object-cover parallax-bg brightness-[0.40]">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-slate-900/50"></div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<main class="relative z-10">

    <!-- HERO SECTION -->
    <section class="min-h-screen flex flex-col items-center justify-center pt-32 pb-12 overflow-hidden relative">
        <div class="container mx-auto px-4 text-center z-20">
            <!-- Animated Hero Title -->
            <h1
                class="hero-title opacity-0 transform translate-y-10 will-change-transform text-5xl sm:text-7xl md:text-9xl font-bold mb-6 font-heading tracking-tight leading-none text-white drop-shadow-2xl">
                Experience the <br />
                <span class="text-white italic pr-2 font-serif">Extraordinary</span>
            </h1>

            <p
                class="hero-subtitle opacity-0 transform translate-y-10 will-change-transform text-lg md:text-2xl text-white/90 mb-12 font-light max-w-2xl mx-auto tracking-wide drop-shadow-lg">
                <?php echo get_setting('hero_subtitle') ?: 'Curated luxury travel experiences designed just for you.'; ?>
            </p>

            <!-- Glass Booking Form -->
            <div class="hero-form opacity-0 transform translate-y-10 max-w-5xl mx-auto w-full relative group">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-white/40 to-white/20 rounded-full blur opacity-30 group-hover:opacity-50 transition duration-1000">
                </div>
                <!-- Search Bar Container -->
                <div
                    class="glass-form p-2 rounded-full relative bg-white/10 border border-white/20 backdrop-blur-xl shadow-2xl">
                    <form action="<?php echo base_url('pages/packages.php'); ?>" method="GET"
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
                                class="w-full pl-12 pr-4 py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none appearance-none cursor-pointer transition-all">
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
                                class="w-full pl-12 pr-4 py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none transition-all"
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
                                class="w-full pl-12 pr-4 py-4 rounded-full bg-white/5 hover:bg-white/20 text-white placeholder-white/70 border border-transparent focus:border-white/40 focus:bg-white/10 outline-none transition-all"
                                placeholder="Guests">
                        </div>

                        <!-- Button -->
                        <button type="submit"
                            class="bg-white text-primary hover:bg-slate-50 font-bold py-4 px-10 rounded-full shadow-lg transform hover:scale-105 transition duration-300 magnetic-btn whitespace-nowrap">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 animate-bounce z-50">
            <a href="#destinations" aria-label="Scroll Down"
                class="text-white hover:text-primary transition-colors duration-300">
                <svg class="w-10 h-10 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3">
                    </path>
                </svg>
            </a>
        </div>
        </div>
    </section>

    <!-- MAIN CONTENT BACKGROUND WRAPPER -->
    <div class="bg-slate-50 relative z-10 pb-12">

        <!-- LIVE STATS BAR (Trust Signals) -->
        <div class="relative z-30 -mt-10 mb-20">
            <div class="container mx-auto px-4">
                <div
                    class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center bg-white/90 backdrop-blur-md">
                    <div class="space-y-1">
                        <p class="text-4xl font-bold text-primary"><?php echo get_setting('stat_trips') ?: '500+'; ?>
                        </p>
                        <p class="text-sm text-slate-500 uppercase tracking-widest font-semibold">Luxury Trips</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-4xl font-bold text-primary"><?php echo get_setting('stat_reviews') ?: '98%'; ?>
                        </p>
                        <p class="text-sm text-slate-500 uppercase tracking-widest font-semibold">5-Star Reviews</p>
                    </div>
                    <!-- Divider for mobile -->
                    <div class="col-span-2 border-t border-slate-100 md:hidden my-2"></div>

                    <div class="space-y-1">
                        <p class="text-4xl font-bold text-primary">
                            <?php echo get_setting('stat_destinations') ?: '25+'; ?>
                        </p>
                        <p class="text-sm text-slate-500 uppercase tracking-widest font-semibold">Destinations</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-4xl font-bold text-primary">
                            <?php echo get_setting('stat_concierge') ?: '24/7'; ?>
                        </p>
                        <p class="text-sm text-slate-500 uppercase tracking-widest font-semibold">Concierge</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESTINATIONS -->
        <section class="py-24 pt-12">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-20 section-header opacity-0 transform translate-y-10">
                    <span class="text-primary font-bold tracking-widest uppercase text-sm">Discover</span>
                    <h2 class="text-4xl md:text-5xl font-heading font-bold text-slate-900 mt-2 mb-6 reveal-text">
                        Trending <span class="text-primary">Destinations</span></h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-primary to-transparent mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-2">
                    <?php foreach ($topDestinations as $index => $dest): ?>
                        <div class="destination-card opacity-0 transform translate-y-10"
                            style="transition-delay: <?php echo $index * 100; ?>ms">
                            <a href="<?php echo destination_url($dest['slug']); ?>"
                                class="block group relative h-[500px] rounded-[2rem] overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500">
                                <img src="<?php echo base_url($dest['image']); ?>"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-60 group-hover:opacity-40 transition-opacity">
                                </div>

                                <div
                                    class="absolute bottom-0 left-0 w-full p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-3xl font-heading font-bold text-white mb-2">
                                        <?php echo htmlspecialchars($dest['name']); ?>
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1 text-yellow-400">
                                            <span>★</span> <span
                                                class="text-white font-medium"><?php echo $dest['rating']; ?></span>
                                        </div>
                                        <span
                                            class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-md transition-transform group-hover:scale-110">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-16">
                    <a href="<?php echo base_url('/destinations'); ?>"
                        class="inline-block px-10 py-4 border border-slate-300 rounded-full text-slate-600 font-medium hover:bg-slate-900 hover:text-white transition-all duration-300 magnetic-btn">View
                        All Destinations</a>
                </div>
            </div>
        </section>

        <!-- WHY CHOOSE US (Features) -->
        <section class="py-20 bg-white border-y border-slate-100">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-16 section-header opacity-0">
                    <span class="text-secondary font-bold tracking-widest uppercase text-sm">Experience</span>
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-slate-900 mt-2">Why Choose <span
                            class="text-primary">IfyTravels</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="feature-card p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 group opacity-0 translate-y-10">
                        <div
                            class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 font-heading">Curated Luxury</h3>
                        <p class="text-slate-600 leading-relaxed">Every destination is hand-picked by our experts to
                            ensure specific standards of luxury and comfort.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 group opacity-0 translate-y-10"
                        style="transition-delay: 100ms;">
                        <div
                            class="w-16 h-16 bg-secondary/10 rounded-2xl flex items-center justify-center mb-6 text-secondary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 font-heading">24/7 Concierge</h3>
                        <p class="text-slate-600 leading-relaxed">Our dedicated support team is available
                            round-the-clock to assist you with any request.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-lg transition-all duration-300 group opacity-0 translate-y-10"
                        style="transition-delay: 200ms;">
                        <div
                            class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <h3 class="text-xl font-bold text-slate-900 font-heading">Best Price Guarantee</h3>
                        </div>
                        <p class="text-slate-600 leading-relaxed">We partner directly with resorts and airlines to bring
                            you exclusive rates you won't find elsewhere.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PACKAGES (Featured) -->
        <?php if (!empty($popularPackages)): ?>
            <section class="py-24 bg-white relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[100px] pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-secondary/5 rounded-full blur-[100px] pointer-events-none">
                </div>

                <div class="container mx-auto px-6 relative z-10">
                    <div
                        class="flex flex-col md:flex-row justify-between items-end mb-16 section-header opacity-0 transform translate-y-10">
                        <div>
                            <span class="text-secondary font-bold tracking-widest uppercase text-sm">Exclusive</span>
                            <h2 class="text-4xl md:text-5xl font-heading font-bold text-slate-900 mt-2 reveal-text">Popular
                                <span class="text-secondary">Packages</span>
                            </h2>
                        </div>
                        <a href="<?php echo base_url('/packages'); ?>"
                            class="hidden md:flex items-center gap-2 text-slate-500 hover:text-primary transition-colors">See
                            all offers <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg></a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <?php foreach ($popularPackages as $index => $pkg): ?>
                            <div class="package-card opacity-0 transform translate-y-10"
                                style="transition-delay: <?php echo $index * 150; ?>ms">
                                <a href="<?php echo package_url($pkg['slug']); ?>"
                                    class="block glass-card-light rounded-3xl overflow-hidden group hover:shadow-2xl transition-all duration-500 bg-white">
                                    <div class="relative h-72 overflow-hidden">
                                        <div
                                            class="absolute top-4 left-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-md">
                                            FEATURED</div>
                                        <img src="<?php echo base_url($pkg['image']); ?>"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            loading="lazy" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                                    </div>
                                    <div class="p-8">
                                        <div class="flex justify-between items-start mb-4">
                                            <h3
                                                class="text-2xl font-bold text-slate-800 group-hover:text-primary transition-colors">
                                                <?php echo htmlspecialchars($pkg['title']); ?>
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-4 text-slate-500 text-sm mb-6">
                                            <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg> <?php echo htmlspecialchars($pkg['duration']); ?></span>
                                            <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg> Global</span>
                                        </div>

                                        <div class="flex items-end justify-between border-t border-slate-100 pt-6">
                                            <div>
                                                <p class="text-xs text-slate-400 uppercase tracking-widest">Starting from</p>
                                                <p class="text-3xl font-heading font-bold text-primary">
                                                    ₹<?php echo number_format($pkg['price']); ?></p>
                                            </div>
                                            <span
                                                class="text-slate-900 font-medium group-hover:translate-x-2 transition-transform">Explore
                                                &rarr;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>



        <!-- TESTIMONIALS (Restored) -->
        <section class="py-24 bg-slate-50 border-t border-slate-200">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-16 section-header opacity-0">
                    <span class="text-primary font-bold tracking-widest uppercase text-sm">Testimonials</span>
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-slate-900 mt-2">Loved by <span
                            class="text-primary">Travelers</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $index => $review): ?>
                            <div class="testimonial-card p-8 bg-white rounded-3xl shadow-lg border border-slate-100 opacity-0 translate-y-10"
                                style="transition-delay: <?php echo $index * 150; ?>ms">
                                <div class="flex items-center gap-4 mb-6">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['name']); ?>&background=random"
                                        alt="<?php echo htmlspecialchars($review['name']); ?>"
                                        class="w-12 h-12 rounded-full ring-2 ring-primary/20">
                                    <div>
                                        <h4 class="font-bold text-slate-900"><?php echo htmlspecialchars($review['name']); ?>
                                        </h4>
                                        <div class="flex text-yellow-500 text-sm">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <span><?php echo ($i < $review['rating']) ? '★' : '☆'; ?></span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-slate-600 italic leading-relaxed">
                                    "<?php echo htmlspecialchars($review['comment']); ?>"</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback Testimonials if DB empty -->
                        <div
                            class="testimonial-card p-8 bg-white rounded-3xl shadow-lg border border-slate-100 opacity-0 translate-y-10">
                            <div class="flex items-center gap-4 mb-6">
                                <img src="https://ui-avatars.com/api/?name=Sarah+J&background=0F766E&color=fff"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <h4 class="font-bold text-slate-900">Sarah Jenkins</h4>
                                    <div class="flex text-yellow-500 text-sm">★★★★★</div>
                                </div>
                            </div>
                            <p class="text-slate-600 italic">"The Maldives trip was absolutely breathtaking. The attention
                                to detail was unmatched."</p>
                        </div>
                        <div
                            class="testimonial-card p-8 bg-white rounded-3xl shadow-lg border border-slate-100 opacity-0 translate-y-10">
                            <div class="flex items-center gap-4 mb-6">
                                <img src="https://ui-avatars.com/api/?name=Michael+R&background=D97706&color=fff"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <h4 class="font-bold text-slate-900">Michael Ross</h4>
                                    <div class="flex text-yellow-500 text-sm">★★★★★</div>
                                </div>
                            </div>
                            <p class="text-slate-600 italic">"Booking was seamless, and the concierge support was a
                                lifesaver during our Paris tour."</p>
                        </div>
                        <div
                            class="testimonial-card p-8 bg-white rounded-3xl shadow-lg border border-slate-100 opacity-0 translate-y-10">
                            <div class="flex items-center gap-4 mb-6">
                                <img src="https://ui-avatars.com/api/?name=Emma+W&background=0F766E&color=fff"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <h4 class="font-bold text-slate-900">Emma Watson</h4>
                                    <div class="flex text-yellow-500 text-sm">★★★★★</div>
                                </div>
                            </div>
                            <p class="text-slate-600 italic">"Highly recommend IfyTravels for anyone looking for a premium,
                                hassle-free vacation."</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- LUXURY CTA SECTION -->
        <section class="py-32 relative overflow-hidden bg-slate-50">
            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="max-w-4xl mx-auto">
                    <span
                        class="inline-block py-2 px-6 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold tracking-[0.2em] mb-8 uppercase">
                        Start Your Journey
                    </span>
                    <h2
                        class="text-5xl md:text-7xl font-heading font-bold mb-8 text-slate-900 leading-tight reveal-text">
                        Ready to Explore the <br /><span class="text-primary">Extraordinary?</span>
                    </h2>
                    <p class="text-xl text-slate-700 mb-12 font-normal max-w-2xl mx-auto drop-shadow-sm">
                        Join the elite travelers who have discovered the world's most breathtaking destinations with
                        ifyTravels.
                    </p>
                    <div class="flex flex-col md:flex-row gap-6 justify-center">
                        <a href="<?php echo base_url('pages/booking.php'); ?>"
                            class="bg-primary text-white font-bold py-5 px-12 rounded-full shadow-lg shadow-primary/30 hover:shadow-primary/40 transform hover:-translate-y-1 transition-all duration-300 text-lg magnetic-btn">
                            Book Your Trip
                        </a>
                        <a href="<?php echo base_url('pages/contact.php'); ?>"
                            class="bg-white border border-slate-200 text-slate-700 font-bold py-5 px-12 rounded-full hover:bg-slate-50 hover:text-primary transition-all duration-300 text-lg magnetic-btn">
                            Contact Concierge
                        </a>
                    </div>
                </div>
            </div>
            <!-- Glow Effects -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px] pointer-events-none">
            </div>
        </section>

        <!-- NEWSLETTER SECTION -->
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-slate-900"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 opacity-30"></div>
            <!-- Pattern -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>

            <div class="container mx-auto px-6 relative z-10 text-center">
                <h2 class="text-3xl md:text-5xl font-heading font-bold text-white mb-4">
                    <?php echo e(get_setting('newsletter_heading', 'Join the Elite Club')); ?>
                </h2>
                <p class="text-slate-300 mb-10 max-w-xl mx-auto text-lg">
                    <?php echo e(get_setting('newsletter_text', 'Subscribe to receive exclusive offers, travel inspiration, and member-only perks directly to your inbox.')); ?>
                </p>

                <form id="newsletter-form" class="max-w-lg mx-auto flex flex-col sm:flex-row gap-4">
                    <input type="email" name="email" placeholder="Your email address"
                        class="flex-1 px-6 py-4 rounded-full bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:bg-white/20 transition-all backdrop-blur-sm"
                        required>
                    <button type="submit"
                        class="px-8 py-4 rounded-full bg-white text-primary font-bold hover:bg-slate-100 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">Subscribe</button>
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
        <script>
            // Register Plugins
            gsap.registerPlugin(ScrollTrigger);

            // 1. Hero Text Reveal (Load Animation)
            const tl = gsap.timeline({ defaults: { ease: "power3.out" } });

            tl.to(".hero-title", { y: 0, opacity: 1, duration: 1.5, delay: 0.2 })
                .to(".hero-subtitle", { y: 0, opacity: 1, duration: 1.2 }, "-=1")
                .to(".hero-form", { y: 0, opacity: 1, duration: 1.2 }, "-=0.8");

            // 2. Parallax Background (Optimized - CSS Fixed handles the heavy lifting, this adds slight depth)
            // Removed heavy GSAP scrub to fix "white space" issue and stuttering. 
            // CSS 'fixed' position is smoother and lighter.

            // 3. Section Headers Reveal
            gsap.utils.toArray('.section-header').forEach(header => {
                gsap.to(header, {
                    scrollTrigger: {
                        trigger: header,
                        start: "top 80%",
                        toggleActions: "play none none reverse"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 1
                });
            });

            // 4. Staggered Cards (Destinations)
            gsap.utils.toArray('.destination-card').forEach(card => {
                gsap.to(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: "top 85%"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 1,
                    ease: "power2.out"
                });
            });

            // 5. Staggered Cards (Packages)
            gsap.utils.toArray('.package-card').forEach(card => {
                gsap.to(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: "top 85%"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 1,
                    ease: "power2.out"
                });
            });

            // 6. Why Choose Us (Features)
            gsap.utils.toArray('.feature-card').forEach(card => {
                gsap.to(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: "top 85%"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 0.8,
                    ease: "back.out(1.7)"
                });
            });

            // 7. Testimonials
            gsap.utils.toArray('.testimonial-card').forEach(card => {
                gsap.to(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: "top 85%"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 1,
                    ease: "power2.out"
                });
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