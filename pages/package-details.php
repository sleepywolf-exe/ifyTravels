<?php
// Package Details Page
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php';

// Support both slug-based and ID-based URLs
$slug = $_GET['slug'] ?? null;
$id = $_GET['id'] ?? null;

if ($slug) {
    $pkg = getPackageBySlug($slug);
} elseif ($id) {
    $pkg = getPackageById($id);
} else {
    $pkg = null;
}

if (!$pkg) {
    // 404 Handling
    $pageTitle = "Package Not Found";
    include __DIR__ . '/../includes/header.php';
    echo '
    <div id="error-state" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4 pt-32 bg-charcoal">
        <h1 class="text-6xl font-bold text-gray-700 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-white mb-2">Package Not Found</h2>
        <a href="' . base_url('pages/packages.php') . '" class="glass-button mt-4">Browse Packages</a>
    </div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$dest = getDestinationById($pkg['destinationId']);
$locationName = $dest ? $dest['name'] . ' (' . $dest['type'] . ')' : 'International';

$pageTitle = $pkg['title'];
include __DIR__ . '/../includes/header.php';
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>",
  "image": "<?php echo base_url($pkg['image']); ?>",
  "description": "<?php echo htmlspecialchars(strip_tags($pkg['description']), ENT_QUOTES); ?>",
  "brand": { "@type": "Brand", "name": "ifyTravels" },
  "offers": {
    "@type": "Offer",
    "url": "<?php echo $metaUrl; ?>",
    "priceCurrency": "INR",
    "price": "<?php echo $pkg['price']; ?>",
    "priceValidUntil": "<?php echo date('Y-12-31'); ?>",
    "availability": "https://schema.org/InStock"
  },
  "review": {
    "@type": "Review",
    "reviewRating": { "@type": "Rating", "ratingValue": "5" },
    "author": { "@type": "Person", "name": "Verified Traveler" }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.9",
    "reviewCount": "<?php echo rand(50, 200); ?>"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "<?php echo base_url(); ?>"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "Packages",
    "item": "<?php echo base_url('pages/packages.php'); ?>"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>",
    "item": "<?php echo $metaUrl; ?>"
  }]
}
</script>

<div id="content-area" class="flex-1 bg-slate-50 min-h-screen transition-colors duration-300">

    <!-- Hero Section -->
    <div class="relative h-[85vh]">
        <div class="absolute inset-0">
            <img src="<?php echo base_url($pkg['image']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                class="w-full h-full object-cover brightness-[0.85] parallax-img">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-black/40"></div>
        </div>

        <div class="absolute inset-0 flex items-end">
            <div class="container mx-auto px-6 pb-20">
                <div class="animate-fade-in-up max-w-5xl">
                    <div class="flex items-center gap-3 mb-6">
                        <span
                            class="inline-block py-1 px-4 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold tracking-[0.2em] uppercase shadow-sm">
                            <?php echo $pkg['duration']; ?>
                        </span>
                        <?php if ($pkg['isPopular']): ?>
                            <span
                                class="inline-block py-1 px-4 rounded-full bg-secondary text-white text-xs font-bold tracking-[0.2em] uppercase shadow-lg">Popular
                                Choice</span>
                        <?php endif; ?>
                    </div>

                    <h1
                        class="text-5xl md:text-7xl font-heading font-bold text-white mb-6 leading-tight drop-shadow-lg reveal-text">
                        <?php echo htmlspecialchars($pkg['title']); ?>
                    </h1>

                    <div class="flex items-center gap-6 text-white text-lg font-light drop-shadow-md">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($locationName); ?></span>
                        </div>
                        <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-white font-medium text-2xl">₹<?php echo number_format($pkg['price']); ?></span>
                            <span class="text-sm self-end mb-1">/ person</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-16 -mt-10 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Left Info -->
            <div class="lg:w-2/3 space-y-12">

                <!-- Overview -->
                <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-xl">
                    <h2 class="text-3xl font-heading font-bold mb-6 text-slate-900 border-b border-slate-100 pb-4">
                        Overview</h2>
                    <p class="text-slate-600 leading-relaxed text-lg font-light">
                        <?php echo $pkg['description']; ?>
                    </p>
                </div>

                <!-- Features Grid -->
                <div>
                    <h2 class="text-3xl font-heading font-bold mb-8 text-slate-900">Package Highlights</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        // Process Activities
                        if (!empty($pkg['activities'])):
                            $acts = is_string($pkg['activities']) ? json_decode($pkg['activities'], true) : $pkg['activities'];
                            if (!empty($acts)): ?>
                                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm md:col-span-2">
                                    <h4 class="text-primary font-bold text-sm uppercase tracking-wide mb-3">Activities</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($acts as $act): ?>
                                            <span
                                                class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-slate-600 text-sm hover:shadow-sm transition"><?php echo htmlspecialchars($act); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; endif; ?>

                        <!-- Standard Features -->
                        <?php foreach ($pkg['features'] as $f): ?>
                            <div
                                class="flex items-center p-4 bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                                <span
                                    class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3 font-bold">✓</span>
                                <span class="text-slate-700"><?php echo htmlspecialchars($f); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Inclusions Carousel -->
                <?php if (!empty($pkg['inclusions'])):
                    $inclusions = is_array($pkg['inclusions']) ? $pkg['inclusions'] : json_decode($pkg['inclusions'], true);
                    if (!empty($inclusions)):
                        // Group into 5
                        $chunks = array_chunk($inclusions, 5);
                        ?>
                        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl overflow-hidden relative">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-heading font-bold text-slate-900">What's Included</h2>
                                <div class="flex gap-2">
                                    <button onclick="prevSlide()"
                                        class="p-2 rounded-full bg-slate-100 text-slate-600 hover:bg-primary hover:text-white transition shadow-sm"><svg
                                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg></button>
                                    <button onclick="nextSlide()"
                                        class="p-2 rounded-full bg-slate-100 text-slate-600 hover:bg-primary hover:text-white transition shadow-sm"><svg
                                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg></button>
                                </div>
                            </div>

                            <div class="relative min-h-[150px]" id="inclusions-carousel">
                                <?php foreach ($chunks as $index => $chunk): ?>
                                    <ul class="space-y-3 transition-all duration-500 absolute w-full top-0 left-0 <?php echo $index === 0 ? 'opacity-100 relative translate-x-0' : 'opacity-0 translate-x-10 hidden'; ?>"
                                        data-slide="<?php echo $index; ?>">
                                        <?php foreach ($chunk as $inc): ?>
                                            <li class="flex items-start text-slate-600">
                                                <svg class="w-5 h-5 mr-3 text-secondary mt-0.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($inc); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; endif; ?>

            </div>

            <!-- Right Sidebar -->
            <aside class="lg:w-1/3">
                <div class="sticky top-32 space-y-6">

                    <!-- Booking Card -->
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xl relative">
                        <!-- Top Gradient -->
                        <div
                            class="bg-gradient-to-br from-secondary to-yellow-600 p-8 relative overflow-hidden text-center">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                            </div>
                            <p class="text-white/90 text-sm font-bold mb-1 uppercase tracking-wide">Total Price</p>
                            <h3 class="text-5xl font-bold text-white mb-2">₹<?php echo number_format($pkg['price']); ?>
                            </h3>
                            <p class="text-white/90 text-sm font-medium">per person (excl. taxes)</p>
                        </div>

                        <div class="p-8 bg-white">
                            <div class="space-y-4 mb-8">
                                <div class="flex justify-between py-3 border-b border-slate-100">
                                    <span class="text-slate-500">Duration</span>
                                    <span class="font-bold text-slate-900"><?php echo $pkg['duration']; ?></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-slate-100">
                                    <span class="text-slate-500">Location</span>
                                    <span
                                        class="font-bold text-slate-900"><?php echo htmlspecialchars($locationName); ?></span>
                                </div>
                            </div>

                            <a href="<?php echo base_url('pages/booking.php?packageId=' . $pkg['id']); ?>"
                                class="block w-full py-4 rounded-xl font-bold text-center bg-primary text-white hover:bg-primary/90 transition shadow-lg shadow-primary/30 transform hover:-translate-y-1 magnetic-btn">
                                Book Now
                            </a>
                            <p class="text-center text-xs text-slate-400 mt-4">Instant Confirmation • Secure Payment</p>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-lg">
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Free Cancellation (7 days prior)
                            </div>
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                24/7 Expert Support
                            </div>
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        gsap.registerPlugin(ScrollTrigger);
        gsap.to(".parallax-img", {
            yPercent: 20, ease: "none",
            scrollTrigger: { trigger: "body", start: "top top", end: "bottom top", scrub: true }
        });
    });

    // Carousel Logic
    let currentSlide = 0;
    const totalSlides = <?php echo isset($chunks) ? count($chunks) : 0; ?>;

    function showSlide(index) {
        if (totalSlides === 0) return;
        const slides = document.querySelectorAll('#inclusions-carousel ul[data-slide]');
        slides.forEach(el => {
            el.classList.add('hidden', 'opacity-0', 'translate-x-10');
            el.classList.remove('opacity-100', 'relative', 'translate-x-0');
        });
        const active = document.querySelector(`#inclusions-carousel ul[data-slide="${index}"]`);
        active.classList.remove('hidden', 'translate-x-10', 'opacity-0');
        active.classList.add('opacity-100', 'relative', 'translate-x-0');
        currentSlide = index;
    }

    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= totalSlides) next = 0;
        showSlide(next);
    }

    function prevSlide() {
        let prev = currentSlide - 1;
        if (prev < 0) prev = totalSlides - 1;
        showSlide(prev);
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>