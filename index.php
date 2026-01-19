<?php
$pageTitle = "Experience the Extraordinary";
$isHome = true;
include 'includes/header.php';
?>

<!-- Link Glassmorphism CSS -->
<link rel="stylesheet" href="assets/css/glassmorphism.css">

<?php
// Get Top 4 Destinations (Prioritize Featured)
$destinations = $destinations ?? [];
$featured = array_filter($destinations, fn($d) => !empty($d['is_featured']));
$others = array_filter($destinations, fn($d) => empty($d['is_featured']));
$topDestinations = array_merge($featured, $others);
$topDestinations = array_slice($topDestinations, 0, 4);

// Get Popular Packages (limit 3)
$packages = $packages ?? [];
$popularPackages = array_filter($packages, fn($p) => $p['isPopular']);
$popularPackages = array_filter($packages, fn($p) => $p['isPopular']);
$popularPackages = array_slice($popularPackages, 0, 3);

// Get Testimonials
$testimonials = [];
try {
    $db = Database::getInstance();
    $testimonials = $db->fetchAll("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3");
} catch (Exception $e) {
    // Fallback to empty
}
?>

<!-- Hero Section with Background Image and Glassmorphism Search Form -->
<section class="relative min-h-screen flex items-center justify-center pt-24 pb-12 mb-0 overflow-hidden"
    style="background-image: url('<?php echo get_setting('hero_bg', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); ?>'); background-size: cover; background-position: center;">

    <!-- Dark Overlay for Better Contrast -->
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="hero-content container mx-auto px-4 relative z-10 w-full">
        <!-- Hero Content -->
        <div class="text-center text-white mb-8 md:mb-12">
            <h1 class="text-3xl sm:text-5xl md:text-7xl font-bold mb-4 md:mb-6 leading-tight drop-shadow-lg">
                <?php echo e(get_setting('hero_title', "Discover the World's Hidden Gems")); ?>
            </h1>
            <div
                class="text-lg md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-light drop-shadow-md px-2 opacity-90 prose prose-invert">
                <?php echo get_setting('hero_subtitle', 'Curated luxury travel experiences designed just for you.'); ?>
            </div>
        </div>

        <!-- Glassmorphism Booking Form -->
        <div class="max-w-5xl mx-auto">
            <div class="glass-form p-5 md:p-8 rounded-2xl">
                <form action="<?php echo base_url('pages/packages.php'); ?>" method="GET"
                    class="space-y-4 md:space-y-6">

                    <!-- Tabs -->
                    <div class="flex justify-center md:justify-start mb-6 md:mb-8">
                        <button type="button" id="tab-packages" onclick="switchTab('packages')"
                            class="px-8 py-3 rounded-full text-white text-lg font-bold transition backdrop-blur-md border border-white/30 shadow-md whitespace-nowrap hover:bg-white/10"
                            style="background: rgba(255, 255, 255, 0.2);">
                            Packages
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                        <!-- Destination Select -->
                        <div class="md:col-span-4">
                            <label class="glass-label text-white mb-1.5 block font-medium text-sm md:text-base">Where
                                to?</label>
                            <div class="relative h-12">
                                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/90 pointer-events-none z-10"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <select name="destination"
                                    class="glass-select w-full h-full !pl-12 appearance-none text-gray-800 text-base md:text-base rounded-xl">
                                    <option value="" class="text-gray-800">Select Destination</option>
                                    <?php if (isset($destinations) && is_array($destinations)): ?>
                                        <?php foreach ($destinations as $dest): ?>
                                            <option value="<?php echo $dest['id']; ?>" class="text-gray-800">
                                                <?php echo htmlspecialchars($dest['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-white/70">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Date Input -->
                        <div class="md:col-span-3">
                            <label
                                class="glass-label text-white mb-1.5 block font-medium text-sm md:text-base">Date</label>
                            <div class="relative h-12">
                                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/90 pointer-events-none z-10"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <input type="text" id="departure-date" name="date"
                                    class="glass-input w-full h-full !pl-12 text-base md:text-base rounded-xl appearance-none placeholder-gray-800"
                                    placeholder="Select Date">
                            </div>
                        </div>

                        <!-- Travelers Input -->
                        <div class="md:col-span-2">
                            <label
                                class="glass-label text-white mb-1.5 block font-medium text-sm md:text-base">Travelers</label>
                            <div class="relative h-12">
                                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/90 pointer-events-none z-10"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <input type="number" name="travelers" min="1"
                                    class="glass-input w-full h-full !pl-12 no-spinner text-base md:text-base rounded-xl"
                                    placeholder="Travelers">
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="md:col-span-3">
                            <label
                                class="glass-label text-transparent mb-1.5 block font-medium select-none hidden md:block">&nbsp;</label>
                            <button type="submit"
                                class="glass-button w-full h-12 flex items-center justify-center font-bold text-sm md:text-base rounded-xl shadow-lg hover:shadow-xl transform active:scale-95 transition-all">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Radio Badges (Class) -->


                </form>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Initialize Flatpickr for date input
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#departure-date", {
            minDate: "today",
            dateFormat: "d-M-Y",
            altInput: true,
            altFormat: "d M, Y",
        });

        // Tab switching function
        window.switchTab = function (tab) {
            // Reset all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(t => {
                t.style.background = 'rgba(255, 255, 255, 0.15)';
            });
            // Highlight clicked tab
            const clickedTab = document.getElementById('tab-' + tab);
            if (clickedTab) {
                clickedTab.style.background = 'rgba(255, 255, 255, 0.3)';
            }
        };

        // Radio button styling
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.closest('.glass-badge')) {
                    // Remove highlight from all in group
                    this.closest('.flex').querySelectorAll('.glass-badge').forEach(b => {
                        b.style.background = 'rgba(255, 255, 255, 0.15)';
                    });
                    // Highlight selected
                    if (this.checked) {
                        this.closest('.glass-badge').style.background = 'rgba(255, 255, 255, 0.3)';
                    }
                }
            });
        });
    });
</script>


<!-- Popular Destinations -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-5xl font-bold mb-4"><span class="text-gradient">Trending Destinations</span></h2>
            <div class="w-24 h-1.5 bg-gradient-to-r from-primary to-teal-400 mx-auto rounded-full mb-4"></div>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Explore the most sought-after travel destinations,
                handpicked by our experts.</p>
        </div>

        <div id="destinations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($topDestinations as $dest): ?>
                <a href="<?php echo destination_url($dest['slug']); ?>"
                    class="group relative rounded-3xl overflow-hidden aspect-[3/4] shadow-xl hover:shadow-2xl cursor-pointer transition-all duration-500">
                    <img src="<?php echo base_url($dest['image']); ?>"
                        class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110"
                        alt="<?php echo htmlspecialchars($dest['name']); ?>"
                        onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-80 group-hover:opacity-90 transition-opacity">
                    </div>

                    <div
                        class="absolute bottom-0 left-0 w-full p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                        <div class="glass-card !bg-white/10 !backdrop-blur-md !border-white/20 p-4 rounded-2xl mb-2">
                            <h3 class="text-2xl font-bold text-white mb-1">
                                <?php echo htmlspecialchars($dest['name']); ?>
                            </h3>
                            <div class="flex items-center justify-between">
                                <p class="text-white/90 text-sm flex items-center gap-1">
                                    <span class="text-yellow-400">★</span>
                                    <?php echo $dest['rating']; ?>
                                </p>
                                <span class="text-white text-xs font-medium px-2 py-1 bg-white/20 rounded-lg">View Guide
                                    &rarr;</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-16">
            <a href="<?php echo base_url('/destinations'); ?>"
                class="inline-flex items-center text-primary font-bold text-lg hover:text-teal-700 transition group">
                <span class="border-b-2 border-primary group-hover:border-teal-700">View All Destinations</span>
                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Special Offers / Featured Packages -->
<?php if (!empty($popularPackages)): ?>
    <section class="py-24 bg-white relative overflow-hidden">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-96 h-96 bg-secondary/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2">
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4"><span class="text-gradient">Special Offers</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">Exclusive deals on our most popular packages - Limited
                    time only!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <?php foreach ($popularPackages as $pkg): ?>
                    <a href="<?php echo package_url($pkg['slug']); ?>"
                        class="glass-card !bg-white group block rounded-3xl overflow-hidden !border-gray-100 !shadow-lg hover:!shadow-2xl">
                        <div class="relative h-64 overflow-hidden">
                            <div
                                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-bold shadow-lg z-10 flex items-center gap-1 text-secondary animate-bounce-slow">
                                <span>🔥</span> Hot Deal
                            </div>
                            <img src="<?php echo base_url($pkg['image']); ?>"
                                alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                        </div>
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-charcoal mb-1 leading-tight">
                                        <?php echo htmlspecialchars($pkg['title']); ?>
                                    </h3>
                                    <p class="text-gray-500 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?php echo htmlspecialchars($pkg['duration']); ?>
                                    </p>
                                </div>
                            </div>

                            <hr class="border-gray-100 my-4">

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-3xl font-bold text-primary">₹<?php echo number_format($pkg['price']); ?>
                                    </div>
                                    <div class="text-xs text-gray-400 font-medium uppercase tracking-wide">per person</div>
                                </div>
                                <span
                                    class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-16">
                <a href="<?php echo base_url('/packages'); ?>"
                    class="glass-button !bg-gray-900 hover:!bg-black !text-white !px-8 !py-4 shadow-xl hover:shadow-2xl">
                    Browse All Packages
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Why Choose Us -->
<section class="py-24 bg-gray-900 text-white relative overflow-hidden">
    <!-- Travel Doodle Background -->
    <div class="absolute inset-0 bg-[url('assets/images/travel-doodles.png')] opacity-10 bg-repeat bg-center"></div>
    <!-- Overlay Gradient to ensure text readability -->
    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 to-transparent"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">Why Choose Us?</h2>
            <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-6"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div
                class="text-center p-8 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition duration-300">
                <div class="w-20 h-20 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Premium Experience</h3>
                <p class="text-gray-400">Hand-picked 5-star accommodations and exclusive access to unique experiences.
                </p>
            </div>

            <div
                class="text-center p-8 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition duration-300">
                <div
                    class="w-20 h-20 bg-secondary/20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">24/7 Support</h3>
                <p class="text-gray-400">We are with you every step of the way, anytime, anywhere in the world.</p>
            </div>

            <div
                class="text-center p-8 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition duration-300">
                <div
                    class="w-20 h-20 bg-teal-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    <svg class="w-10 h-10 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Best Price Guarantee</h3>
                <p class="text-gray-400">Unbeatable value for unforgettable memories with transparent pricing.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-5xl font-bold mb-4"><span class="text-gradient">What Our Travelers Say</span></h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Real experiences from real travelers</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $t): ?>
                    <div
                        class="bg-white p-10 rounded-3xl shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border border-gray-100 relative">
                        <!-- Quote Icon -->
                        <div class="absolute top-8 right-8 text-primary/10">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V11C14.017 11.5523 13.5693 12 13.017 12H12.017V5H22.017V15C22.017 18.3137 19.3307 21 16.017 21H14.017ZM5.0166 21L5.0166 18C5.0166 16.8954 5.91203 16 7.0166 16H10.0166C10.5689 16 11.0166 15.5523 11.0166 15V9C11.0166 8.44772 10.5689 8 10.0166 8H6.0166C5.46432 8 5.0166 8.44772 5.0166 9V11C5.0166 11.5523 4.56889 12 4.0166 12H3.0166V5H13.0166V15C13.0166 18.3137 10.3303 21 7.0166 21H5.0166Z">
                                </path>
                            </svg>
                        </div>

                        <div class="flex items-center mb-6">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-primary to-teal-400 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4 shadow-lg shrink-0">
                                <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-charcoal text-lg"><?php echo htmlspecialchars($t['name']); ?></h4>
                                <div class="text-yellow-400 text-sm flex gap-0.5">
                                    <?php
                                    $rating = $t['rating'];
                                    echo str_repeat('★', $rating);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic leading-relaxed relative z-10">
                            "<?php echo htmlspecialchars($t['message']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center text-gray-500">
                    <p>No testimonials yet. Be the first to share your experience!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<!-- CTA Section -->
<section class="py-20 relative overflow-hidden">
    <!-- Background with Doodles -->
    <div class="absolute inset-0 bg-gray-900">
        <div class="absolute inset-0 bg-[url('assets/images/travel-doodles.png')] opacity-10 bg-repeat bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-teal-900/90"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div
            class="glass-card max-w-4xl mx-auto rounded-3xl p-10 md:p-16 text-center transform transition duration-500 hover:scale-[1.01] border border-white/20 relative overflow-hidden group">

            <!-- Decorative Glow -->
            <div
                class="absolute -top-24 -right-24 w-64 h-64 bg-secondary/30 rounded-full blur-3xl group-hover:bg-secondary/40 transition duration-700">
            </div>
            <div
                class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/30 rounded-full blur-3xl group-hover:bg-primary/40 transition duration-700">
            </div>

            <div class="relative z-10">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold tracking-wider mb-6 uppercase">
                    Start Your Journey
                </span>

                <h2 class="text-4xl md:text-6xl font-bold mb-6 text-white font-heading">
                    Ready to Explore the <br /><span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-white to-teal-100">Unknown?</span>
                </h2>

                <p class="text-xl mb-10 text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Join thousands of happy travelers who have discovered their dream destinations with us. Your next
                    great adventure is just a click away.
                </p>

                <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                    <a href="<?php echo base_url('pages/booking.php'); ?>"
                        class="bg-white text-primary font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition shadow-xl hover:shadow-2xl hover:shadow-white/20 transform hover:-translate-y-1 w-full md:w-auto">
                        Book Your Trip Now
                    </a>
                    <a href="<?php echo base_url('pages/contact.php'); ?>"
                        class="bg-transparent hover:bg-white/10 text-white font-bold py-4 px-10 rounded-full transition border-2 border-white/30 hover:border-white w-full md:w-auto flex items-center justify-center gap-2 group-btn">
                        <span>Contact Us</span>
                        <svg class="w-5 h-5 group-btn-hover:translate-x-1 transition" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>