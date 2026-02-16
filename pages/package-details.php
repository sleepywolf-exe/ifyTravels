<?php
// Package Details Page
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php';
require_once __DIR__ . '/../includes/classes/SchemaGenerator.php';
require_once __DIR__ . '/../includes/classes/InternalLinker.php';

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
    http_response_code(404);
    $errorCode = 404;
    $errorTitle = "Package Not Found";
    $errorMessage = "We couldn't find the package you're looking for. It might have been removed or renamed.";
    include __DIR__ . '/error.php';
    exit;
}

$dest = getDestinationById($pkg['destinationId']);
$locationName = $dest ? $dest['name'] . ' (' . $dest['type'] . ')' : 'International';

$pageTitle = $pkg['title'];
$pageDescription = mb_substr(strip_tags($pkg['description']), 0, 155) . '...';
$pageImage = $pkg['image'];

// Facebook CAPI: ViewContent
if (isset($fbCapi)) {
    $fbCapi->sendEvent('ViewContent', [
        'content_type' => 'product',
        'content_ids' => [$pkg['id']],
        'content_name' => $pkg['title'],
        'content_category' => 'Package',
        'value' => $pkg['price'],
        'currency' => 'INR'
    ], $userData);
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Dynamic Schema (Auto-Generated) -->
<?php
// Product Schema
$productSchema = SchemaGenerator::getTourPackage($pkg);
echo SchemaGenerator::render($productSchema);

// Breadcrumb Schema
$breadcrumbSchema = SchemaGenerator::getBreadcrumb([
    'Home' => base_url(),
    'Packages' => base_url('packages'),
    $pkg['title'] => $metaUrl
]);
echo SchemaGenerator::render($breadcrumbSchema);
?>

<div id="content-area" class="flex-1 bg-white min-h-screen relative overflow-hidden">

    <!-- Massive Background Text (Visible) -->
    <div
        class="absolute top-[85vh] left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0 overflow-hidden">
        <h2
            class="text-[6rem] md:text-[20rem] font-black text-slate-900 opacity-[0.03] leading-none tracking-tighter uppercase font-heading whitespace-nowrap transform -translate-y-1/4">
            JOURNEY
        </h2>
    </div>

    <!-- Hero Section - Magazine Style -->
    <div class="relative min-h-[65vh] md:h-[90vh] overflow-hidden z-10 flex items-center">
        <div class="absolute inset-0 h-[120%] w-full -top-[10%] parallax-container">
            <img src="<?php echo base_url($pkg['image']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                class="w-full h-full object-cover brightness-[0.85]">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/40"></div>
        </div>

        <div class="absolute inset-0 flex items-center pt-20 md:pt-0">
            <div class="container mx-auto px-6">
                <div class="max-w-7xl">
                    <div class="flex items-center gap-4 mb-8 animate-fade-in-up">
                        <span
                            class="inline-block py-2 px-6 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold tracking-[0.2em] uppercase text-sm shadow-lg">
                            Hand-Crafted Journey
                        </span>
                        <?php if ($pkg['isPopular']): ?>
                            <span
                                class="inline-block py-2 px-6 rounded-full bg-primary/90 backdrop-blur-md text-white font-bold tracking-[0.2em] uppercase text-sm shadow-lg">
                                Popular
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1
                        class="text-4xl md:text-6xl lg:text-8xl font-heading font-black text-white mb-6 leading-tight drop-shadow-2xl animate-fade-in-up">
                        <?php echo htmlspecialchars($pkg['title']); ?>
                    </h1>

                    <div class="flex items-center gap-6 text-white text-lg font-light drop-shadow-lg animate-fade-in-up"
                        style="animation-delay: 0.2s">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-xl"><?php echo htmlspecialchars($locationName); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Info Card -->
        <div class="absolute bottom-32 right-12 hidden lg:block animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-3xl shadow-2xl max-w-sm">
                <div class="flex items-center gap-4 mb-2">
                    <p class="text-white/60 text-sm uppercase tracking-wider font-semibold">Starting From</p>
                </div>
                <div class="flex items-baseline gap-2 mb-4">
                    <span
                        class="text-white text-4xl font-bold font-heading">₹<?php echo number_format($pkg['price']); ?></span>
                    <span class="text-white/80 text-sm">/ person</span>
                </div>
                <div class="flex items-center gap-2 text-yellow-400">
                    <span class="text-xl">★★★★★</span>
                    <span class="text-white text-sm font-bold ml-2">4.9/5.0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Bar (Glassmorphism) -->
    <div class="relative z-20 -mt-16 md:-mt-24 container mx-auto px-4 md:px-6 mb-12 md:mb-20">
        <div
            class="bg-white shadow-xl rounded-[2rem] md:rounded-[2.5rem] border border-slate-100 p-5 md:p-8 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 divide-x divide-slate-100">
            <!-- Duration -->
            <div class="px-4 text-center md:text-left">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Duration</p>
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-800 font-bold text-sm md:text-lg">
                        <?php echo htmlspecialchars($pkg['duration']); ?>
                    </p>
                </div>
            </div>
            <!-- Location -->
            <div class="px-4 text-center md:text-left">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Location</p>
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-slate-800 font-bold text-sm md:text-lg truncate">
                        <?php echo htmlspecialchars($dest['country']); ?>
                    </p>
                </div>
            </div>
            <!-- Type -->
            <div class="px-4 text-center md:text-left">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Trip Type</p>
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <p class="text-slate-800 font-bold text-sm md:text-lg truncate">Adventure</p>
                </div>
            </div>
            <!-- Rating -->
            <div class="px-4 text-center md:text-left border-r-0">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Rating</p>
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-800 font-bold text-sm md:text-lg">4.9 <span
                                class="text-slate-400 text-xs md:text-sm font-normal">/ 5</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-16 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Left Info -->
            <div class="lg:w-2/3 space-y-12">

                <!-- Overview -->
                <!-- Overview - Editorial Style -->
                <div class="mb-10 md:mb-16">
                    <div class="w-20 h-2 bg-primary mb-6 md:mb-8 rounded-full"></div>
                    <h2 class="text-3xl md:text-7xl font-heading font-black mb-6 md:mb-10 text-slate-900 leading-tight">
                        About This
                        <span class="block text-primary italic font-serif font-light text-5xl md:text-8xl mt-2">
                            Adventure
                        </span>
                    </h2>
                    <div class="prose prose-lg text-slate-600 font-light leading-relaxed text-xl">
                        <?php
                        $linker = InternalLinker::getInstance();
                        echo $linker->linkContent($pkg['description']);
                        ?>
                    </div>
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
                                                <svg class="w-5 h-5 mr-3 text-primary mt-0.5" fill="none" stroke="currentColor"
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

                <!-- GEO-Optimized FAQ Section -->
                <?php
                $pkgTitle = htmlspecialchars($pkg['title']);
                $pkgFaqs = [
                    [
                        "question" => "Is airfare included in the $pkgTitle package?",
                        "answer" => "Flight inclusions depend on the specific package variant. Please check the 'Inclusions' section above or contact our concierge for flight-inclusive quotes."
                    ],
                    [
                        "question" => "Can I customize the itinerary for $pkgTitle?",
                        "answer" => "Absolutely. All ifyTravels packages are 100% customizable. You can add extra nights, upgrade hotels, or include unique experiences to suit your preferences."
                    ],
                    [
                        "question" => "What is the cancellation policy?",
                        "answer" => "We offer flexible cancellation up to 7 days before departure for a full refund (excluding flight costs). For last-minute changes, our team will work to minimize any fees."
                    ],
                    [
                        "question" => "Are meals included?",
                        "answer" => "Most of our packages include daily breakfast. Some premium options may also include dinners or all-inclusive meal plans. Refer to the 'Inclusions' tab for specific details."
                    ]
                ];
                ?>
                <!-- FAQ Schema -->
                <script type="application/ld+json">
                {
                  "@context": "https://schema.org",
                  "@type": "FAQPage",
                  "mainEntity": [
                    <?php
                    $faqCount = count($pkgFaqs);
                    foreach ($pkgFaqs as $i => $faq) {
                        echo '{';
                        echo '"@type": "Question",';
                        echo '"name": "' . $faq['question'] . '",';
                        echo '"acceptedAnswer": {';
                        echo '"@type": "Answer",';
                        echo '"text": "' . $faq['answer'] . '"';
                        echo '}';
                        echo '}' . ($i < $faqCount - 1 ? ',' : '');
                    }
                    ?>
                  ]
                }
                </script>

                <div class="mt-12">
                    <h2 class="text-2xl font-heading font-bold mb-6 text-slate-900">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        <?php foreach ($pkgFaqs as $faq): ?>
                            <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                <details class="group">
                                    <summary
                                        class="flex justify-between items-center p-5 cursor-pointer list-none hover:bg-white transition-colors">
                                        <h3 class="font-bold text-slate-700 text-sm md:text-base">
                                            <?php echo $faq['question']; ?>
                                        </h3>
                                        <span class="transition-transform duration-300 group-open:rotate-180 text-primary">
                                            <svg fill="none" height="20" width="20" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                            </svg>
                                        </span>
                                    </summary>
                                    <div
                                        class="text-slate-600 px-5 pb-5 text-sm leading-relaxed border-t border-slate-100 bg-white">
                                        <?php echo $faq['answer']; ?>
                                    </div>
                                </details>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar -->
            <aside class="lg:w-1/3">
                <div class="sticky top-32 space-y-6">

                    <!-- Booking Card -->
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xl relative">
                        <!-- Top Gradient -->
                        <div
                            class="bg-gradient-to-br from-primary to-teal-600 p-8 relative overflow-hidden text-center">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                            </div>
                            <p class="text-white/90 text-sm font-bold mb-1 uppercase tracking-wide">Total Price</p>
                            <h3 class="text-5xl font-bold text-white mb-2">
                                ₹<?php echo number_format($pkg['price']); ?>
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

                            <a href="<?php echo base_url('booking?packageId=' . $pkg['id']); ?>"
                                class="block w-full py-4 rounded-xl font-bold text-center bg-primary text-white hover:bg-primary/90 transition shadow-lg shadow-primary/30">
                                Book Now
                            </a>
                            <p class="text-center text-xs text-slate-400 mt-4">Instant Confirmation • Secure Payment
                            </p>
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
        gsap.to(".parallax-container", {
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

<!-- Mobile Fixed Booking Bar (App Like) -->
<div
    class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 z-[9999] px-6 py-4 pb-safe shadow-[0_-5px_30px_rgba(0,0,0,0.1)]">
    <div class="flex items-center justify-between gap-4">
        <div>
            <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Total Price</span>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-black text-slate-900">₹<?php echo number_format($pkg['price']); ?></span>
                <span class="text-xs text-slate-400 font-medium">/ person</span>
            </div>
        </div>
        <a href="<?php echo base_url('booking?packageId=' . $pkg['id']); ?>"
            class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-lg shadow-lg shadow-primary/30 active:scale-95 transition-transform">
            Book Now
        </a>
    </div>
</div>

<?php
$hideMobileNav = true;
include __DIR__ . '/../includes/footer.php';
?>