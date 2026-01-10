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
                    <div class="flex justify-start mb-6 md:mb-8">
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
                                    class="glass-input w-full h-full !pl-12 text-base md:text-base rounded-xl"
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
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-charcoal mb-4">Trending Destinations</h2>
            <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-3"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Explore the most sought-after travel destinations, handpicked by
                our experts</p>
        </div>

        <div id="destinations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($topDestinations as $dest): ?>
                <a href="<?php echo destination_url($dest['slug']); ?>"
                    class="group relative rounded-2xl overflow-hidden aspect-[3/4] shadow-lg cursor-pointer transform hover:-translate-y-2 transition-all duration-300">
                    <img src="<?php echo base_url($dest['image']); ?>"
                        class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110"
                        alt="<?php echo htmlspecialchars($dest['name']); ?>"
                        onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white">
                        <h3 class="text-2xl font-bold mb-1">
                            <?php echo htmlspecialchars($dest['name']); ?>
                        </h3>
                        <p class="text-gray-300 text-sm flex items-center gap-1">
                            <span class="text-yellow-400">★</span>
                            <?php echo $dest['rating']; ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo base_url('/destinations'); ?>"
                class="inline-flex items-center text-primary font-semibold hover:text-teal-700 transition">
                View All Destinations
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Special Offers / Featured Packages -->
<?php if (!empty($popularPackages)): ?>
    <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-charcoal mb-4">Special Offers</h2>
                <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-3"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Exclusive deals on our most popular packages - Limited time only!
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($popularPackages as $pkg): ?>
                    <a href="<?php echo package_url($pkg['slug']); ?>"
                        class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group block">
                        <div class="relative h-56 overflow-hidden">
                            <div
                                class="absolute top-4 right-4 bg-secondary text-white px-3 py-1 rounded-full text-sm font-bold z-10">
                                🔥 Hot Deal
                            </div>
                            <img src="<?php echo base_url($pkg['image']); ?>"
                                alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-charcoal mb-2"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            <p class="text-gray-600 text-sm mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?php echo htmlspecialchars($pkg['duration']); ?>
                            </p>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="text-2xl font-bold text-primary">₹<?php echo number_format($pkg['price']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">per person</div>
                                </div>
                                <span
                                    class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">
                                    View Details
                                </span>
                            </div>
                        </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo base_url('/packages'); ?>"
                class="inline-flex items-center text-primary font-semibold hover:text-teal-700 transition">
                Browse All Packages
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        </div>
    </section>
<?php endif; ?>

<!-- Why Choose Us -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-charcoal mb-4">Why Choose Us</h2>
            <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-3"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">We provide exceptional service and unforgettable experiences</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div
                class="text-center p-8 group hover:-translate-y-2 transition duration-300 bg-white rounded-2xl hover:shadow-xl">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary/5 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-charcoal">Premium Experience</h3>
                <p class="text-gray-600">Hand-picked 5-star accommodations and exclusive access to unique experiences.
                </p>
            </div>

            <div
                class="text-center p-8 group hover:-translate-y-2 transition duration-300 bg-white rounded-2xl hover:shadow-xl">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-secondary/20 to-secondary/5 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-charcoal">24/7 Support</h3>
                <p class="text-gray-600">We are with you every step of the way, anytime, anywhere in the world.</p>
            </div>

            <div
                class="text-center p-8 group hover:-translate-y-2 transition duration-300 bg-white rounded-2xl hover:shadow-xl">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-teal-600/20 to-teal-600/5 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3 text-charcoal">Best Price Guarantee</h3>
                <p class="text-gray-600">Unbeatable value for unforgettable memories with transparent pricing.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works / Process -->
<section class="py-20 bg-gradient-to-br from-primary/5 to-teal-600/5">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-charcoal mb-4">How It Works</h2>
            <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-3"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Plan your perfect trip in 4 simple steps</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center relative">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-primary to-teal-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">
                    1</div>
                <h3 class="text-lg font-bold mb-2 text-charcoal">Choose Destination</h3>
                <p class="text-gray-600 text-sm">Browse our curated list of amazing destinations</p>
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300 text-4xl">→</div>
            </div>

            <div class="text-center relative">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-primary to-teal-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">
                    2</div>
                <h3 class="text-lg font-bold mb-2 text-charcoal">Select Package</h3>
                <p class="text-gray-600 text-sm">Pick the perfect package that suits your needs</p>
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300 text-4xl">→</div>
            </div>

            <div class="text-center relative">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-primary to-teal-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">
                    3</div>
                <h3 class="text-lg font-bold mb-2 text-charcoal">Book & Pay</h3>
                <p class="text-gray-600 text-sm">Secure your reservation with easy payment options</p>
                <div class="hidden md:block absolute top-8 -right-4 text-gray-300 text-4xl">→</div>
            </div>

            <div class="text-center">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-primary to-teal-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg">
                    4</div>
                <h3 class="text-lg font-bold mb-2 text-charcoal">Enjoy Your Trip</h3>
                <p class="text-gray-600 text-sm">Relax and create unforgettable memories</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-charcoal mb-4">What Our Travelers Say</h2>
            <div class="w-20 h-1 bg-secondary mx-auto rounded-full mb-3"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Real experiences from real travelers</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $t): ?>
                    <div class="bg-gray-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition group">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-primary to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-charcoal"><?php echo htmlspecialchars($t['name']); ?></h4>
                                <div class="text-yellow-400 text-sm">
                                    <?php
                                    $rating = $t['rating'];
                                    echo str_repeat('★', $rating);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">"<?php echo htmlspecialchars($t['message']); ?>"</p>
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
<section class="py-20 bg-gradient-to-r from-primary to-teal-600 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Start Your Adventure?</h2>
        <p class="text-xl mb-10 max-w-2xl mx-auto opacity-90">Join thousands of happy travelers who have discovered
            their dream destinations with us</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="<?php echo base_url('pages/booking.php'); ?>"
                class="bg-white text-primary font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition transform hover:scale-105 shadow-xl">
                Book Your Trip Now
            </a>
            <a href="<?php echo base_url('pages/contact.php'); ?>"
                class="bg-white/20 backdrop-blur-md hover:bg-white/30 text-white font-bold py-4 px-10 rounded-full transition border border-white/30 shadow-xl">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>