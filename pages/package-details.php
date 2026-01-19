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
    ?>
    <div id="error-state" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4 pt-32">
        <h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-charcoal mb-2">Package Not Found</h2>
        <a href="<?php echo base_url('packages'); ?>"
            class="bg-primary text-white px-6 py-2 rounded-lg font-bold mt-4">Browse Packages</a>
    </div>
    <?php
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$dest = getDestinationById($pkg['destinationId']);
$locationName = $dest ? $dest['name'] . ' (' . $dest['type'] . ')' : 'International';

$pageTitle = $pkg['title'];
include __DIR__ . '/../includes/header.php';
?>

<!-- Schema.org Markup for Rich Snippets -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>",
  "image": "<?php echo base_url($pkg['image']); ?>",
  "description": "<?php echo htmlspecialchars(strip_tags($pkg['description']), ENT_QUOTES); ?>",
  "brand": {
    "@type": "Brand",
    "name": "ifyTravels"
  },
  "offers": {
    "@type": "Offer",
    "url": "<?php echo $metaUrl; ?>",
    "priceCurrency": "INR",
    "price": "<?php echo $pkg['price']; ?>",
    "priceValidUntil": "<?php echo date('Y-12-31'); ?>",
    "availability": "https://schema.org/InStock",
    "itemCondition": "https://schema.org/NewCondition"
  },
  "review": {
    "@type": "Review",
    "reviewRating": {
      "@type": "Rating",
      "ratingValue": "5",
      "bestRating": "5"
    },
    "author": {
      "@type": "Person",
      "name": "Verified Traveler"
    }
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
    "item": "<?php echo base_url('packages'); ?>"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>",
    "item": "<?php echo $metaUrl; ?>"
  }]
}
</script>

<div class="pt-24 pb-12 container mx-auto px-6 flex-1" id="content-area">
    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Content -->
        <div class="lg:w-2/3">
            <nav class="flex mb-4 text-sm text-gray-500">
                <a href="<?php echo base_url(); ?>" class="hover:text-primary">Home</a> <span class="mx-2">/</span>
                <a href="<?php echo base_url('packages'); ?>" class="hover:text-primary">Packages</a> <span
                    class="mx-2">/</span>
                <span id="breadcrumb-title" class="text-gray-800 font-medium">
                    <?php echo htmlspecialchars($pkg['title']); ?>
                </span>
            </nav>

            <h1 id="pkg-title" class="text-4xl font-bold mb-4 text-charcoal">
                <?php echo htmlspecialchars($pkg['title']); ?>
            </h1>
            <div class="flex items-center space-x-4 text-gray-500 mb-6 text-sm font-medium">
                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <?php echo $pkg['duration']; ?>
                </span>
                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <?php echo htmlspecialchars($locationName); ?>
                </span>
            </div>

            <div class="rounded-2xl overflow-hidden mb-8 h-96 shadow-md border border-gray-100">
                <img src="<?php echo base_url($pkg['image']); ?>" class="w-full h-full object-cover">
            </div>

            <h2 class="text-2xl font-bold mb-4 text-charcoal">Overview</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">
                <?php echo $pkg['description']; ?>
            </p>

            <h2 class="text-2xl font-bold mb-4 mt-12 text-charcoal">Package Features</h2>
            <ul id="pkg-features" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <!-- Activities -->
                <?php if (!empty($pkg['activities'])):
                    $acts = is_string($pkg['activities']) ? json_decode($pkg['activities'], true) : $pkg['activities'];
                    if (!empty($acts)) { ?>
                        <li class="flex items-start text-gray-700 p-3 bg-gray-50 rounded-lg col-span-1 md:col-span-2">
                            <span
                                class="w-6 h-6 bg-white rounded-full flex items-center justify-center text-green-500 shadow-sm mr-3 font-bold text-xs flex-shrink-0">✓</span>
                            <div>
                                <span class="font-bold">Tour Activities:</span>
                                <?php echo htmlspecialchars(implode(', ', $acts)); ?>
                            </div>
                        </li>
                    <?php }endif; ?>

                <!-- Themes -->
                <?php if (!empty($pkg['themes'])):
                    $themes = is_string($pkg['themes']) ? json_decode($pkg['themes'], true) : $pkg['themes'];
                    if (!empty($themes)) { ?>
                        <li class="flex items-start text-gray-700 p-3 bg-gray-50 rounded-lg col-span-1 md:col-span-2">
                            <span
                                class="w-6 h-6 bg-white rounded-full flex items-center justify-center text-green-500 shadow-sm mr-3 font-bold text-xs flex-shrink-0">✓</span>
                            <div>
                                <span class="font-bold">Tour Themes:</span>
                                <?php echo htmlspecialchars(implode(', ', $themes)); ?>
                            </div>
                        </li>
                    <?php }endif; ?>

                <!-- Standard Features -->
                <?php foreach ($pkg['features'] as $f): ?>
                    <li class="flex items-center text-gray-700 p-3 bg-gray-50 rounded-lg">
                        <span
                            class="w-6 h-6 bg-white rounded-full flex items-center justify-center text-green-500 shadow-sm mr-3 font-bold text-xs">✓</span>
                        <?php echo htmlspecialchars($f); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right Sidebar -->
        <aside class="lg:w-1/3">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 sticky top-24 overflow-hidden">
                <!-- Price Section -->
                <div class="bg-gradient-to-br from-primary to-teal-600 p-6 text-white">
                    <div class="text-sm font-medium mb-2 opacity-90">Starting From</div>
                    <div class="text-4xl font-bold mb-1">₹<?php echo number_format($pkg['price']); ?></div>
                    <div class="text-sm opacity-90">per person</div>
                    <div class="mt-4 flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">Available Now - Book Today!</span>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Duration & Location -->
                    <div class="mb-6">
                        <h3 class="font-bold text-charcoal mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Trip Details
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Duration</span>
                                <span
                                    class="font-semibold text-charcoal"><?php echo htmlspecialchars($pkg['duration']); ?></span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Location</span>
                                <span
                                    class="font-semibold text-charcoal"><?php echo htmlspecialchars($locationName); ?></span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Destination Covered</span>
                                <span
                                    class="font-semibold text-charcoal text-right"><?php echo htmlspecialchars(!empty($pkg['destination_covered']) ? $pkg['destination_covered'] : $dest['name']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Inclusions (Carousel) -->
                    <?php if (!empty($pkg['inclusions'])):
                        $inclusions = is_array($pkg['inclusions']) ? $pkg['inclusions'] : json_decode($pkg['inclusions'], true);
                        if (empty($inclusions))
                            $inclusions = [];

                        // Chunk items into groups of 5
                        $chunks = array_chunk($inclusions, 5);
                        if (!empty($chunks)):
                            ?>
                            <div class="mb-6" id="inclusions-carousel">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-bold text-charcoal flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        What's Included
                                    </h3>
                                    <!-- Controls -->
                                    <?php if (count($chunks) > 1): ?>
                                        <div class="flex space-x-1">
                                            <button onclick="prevSlide()"
                                                class="p-1 rounded-full hover:bg-gray-100 text-gray-500 hover:text-primary transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button onclick="nextSlide()"
                                                class="p-1 rounded-full hover:bg-gray-100 text-gray-500 hover:text-primary transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="relative overflow-hidden min-h-[160px]">
                                    <?php foreach ($chunks as $index => $chunk): ?>
                                        <ul class="space-y-2 transition-all duration-300 absolute w-full top-0 left-0 <?php echo $index === 0 ? 'opacity-100 relative translate-x-0' : 'opacity-0 translate-x-full hidden'; ?>"
                                            data-slide="<?php echo $index; ?>">
                                            <?php foreach ($chunk as $feature): ?>
                                                <li class="flex items-start text-sm text-gray-700">
                                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span><?php echo htmlspecialchars($feature); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <script>
                                let currentSlide = 0;
                                const totalSlides = <?php echo count($chunks); ?>;

                                function showSlide(index) {
                                    const slides = document.querySelectorAll('#inclusions-carousel ul[data-slide]');
                                    slides.forEach(el => {
                                        el.classList.add('hidden', 'opacity-0', 'translate-x-full');
                                        el.classList.remove('opacity-100', 'relative', 'translate-x-0');
                                    });

                                    const active = document.querySelector(`#inclusions-carousel ul[data-slide="${index}"]`);
                                    active.classList.remove('hidden', 'translate-x-full', 'opacity-0');
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
                        <?php endif; endif; ?>

                    <!-- Special Offer -->
                    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold text-amber-900">Limited Time Offer</span>
                        </div>
                        <p class="text-sm text-amber-800">Book now and save up to 15% on early bird bookings!</p>
                    </div>

                    <!-- CTA Button -->
                    <a href="<?php echo base_url('pages/booking.php?packageId=' . $pkg['id']); ?>"
                        class="block w-full bg-gradient-to-r from-primary to-teal-600 text-white text-center font-bold py-4 rounded-xl hover:shadow-2xl transition-all duration-300 shadow-lg mb-4 transform hover:-translate-y-1">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Book This Package
                    </a>

                    <!-- Trust Indicators -->
                    <?php if (!empty($pkg['trust_badges'])):
                        // $badges is already an array thanks to functions.php
                        $badges = $pkg['trust_badges'];
                        if (!empty($badges)):
                            $allBadges = [
                                'secure_payment' => 'Secure Payment Gateway',
                                'customer_support' => '24/7 Customer Support',
                                'free_cancellation' => 'Free Cancellation (7 days prior)',
                                'verified_operator' => 'Verified Operator',
                                'best_price' => 'Best Price Guarantee'
                            ];
                            $badgeDetails = [
                                'secure_payment' => [
                                    'inner' => '<path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
                                    'color' => 'text-green-500',
                                    'viewBox' => '0 0 20 20',
                                    'fill' => 'currentColor',
                                    'stroke' => 'none'
                                ],
                                'customer_support' => [
                                    'inner' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />',
                                    'color' => 'text-blue-500',
                                    'viewBox' => '0 0 20 20',
                                    'fill' => 'currentColor',
                                    'stroke' => 'none'
                                ],
                                'free_cancellation' => [
                                    'inner' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />',
                                    'color' => 'text-red-500',
                                    'viewBox' => '0 0 20 20',
                                    'fill' => 'currentColor',
                                    'stroke' => 'none'
                                ],
                                'verified_operator' => [
                                    'inner' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                    'color' => 'text-purple-500',
                                    'viewBox' => '0 0 24 24', // Notice the 24 24 viewbox for stroke icons usually
                                    'fill' => 'none',
                                    'stroke' => 'currentColor'
                                ],
                                'best_price' => [
                                    'inner' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                    'color' => 'text-yellow-500',
                                    'viewBox' => '0 0 24 24',
                                    'fill' => 'none',
                                    'stroke' => 'currentColor'
                                ]
                            ];
                            ?>
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <?php foreach ($badges as $badgeKey):
                                    if (!isset($badgeDetails[$badgeKey]))
                                        continue;
                                    $b = $badgeDetails[$badgeKey];
                                    ?>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 <?php echo $b['color']; ?>" fill="<?php echo $b['fill']; ?>"
                                            stroke="<?php echo $b['stroke']; ?>" viewBox="<?php echo $b['viewBox']; ?>">
                                            <?php echo $b['inner']; ?>
                                        </svg>
                                        <span><?php echo $allBadges[$badgeKey] ?? ucwords(str_replace('_', ' ', $badgeKey)); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; endif; ?>
                </div>
            </div>
        </aside>
    </div>
</div>


</div>
</div>
</aside>
</div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>