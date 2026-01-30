<?php
// Destination Details Page
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php';

// Support both slug-based and ID-based URLs
$slug = $_GET['slug'] ?? null;
$id = $_GET['id'] ?? null;

if ($slug) {
    $dest = getDestinationBySlug($slug);
} elseif ($id) {
    $dest = getDestinationById($id);
} else {
    $dest = null;
}

if (!$dest) {
    // 404 Handling
    $pageTitle = "Destination Not Found";
    include __DIR__ . '/../includes/header.php';
    echo '
    <div id="error-state" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4 pt-32 bg-charcoal">
        <h1 class="text-6xl font-bold text-gray-700 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-white mb-2">Destination Not Found</h2>
        <a href="' . base_url('pages/destinations.php') . '" class="glass-button mt-4">Browse Destinations</a>
    </div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// FIX: Ensure we have the ID for fetching packages
$id = $dest['id'];

$pageTitle = $dest['name'];
include __DIR__ . '/../includes/header.php';
$packages = getPackagesByDestination($id);
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "TouristDestination",
  "name": "<?php echo htmlspecialchars($dest['name'], ENT_QUOTES); ?>",
  "description": "<?php echo htmlspecialchars(strip_tags($dest['description']), ENT_QUOTES); ?>",
  "image": "<?php echo base_url($dest['image']); ?>",
  "touristType": "<?php echo htmlspecialchars($dest['type'], ENT_QUOTES); ?>",
  "geo": {
    "@type": "GeoCoordinates",
    "addressCountry": "<?php echo htmlspecialchars($dest['country'], ENT_QUOTES); ?>"
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
    "name": "Destinations",
    "item": "<?php echo base_url('pages/destinations.php'); ?>"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "<?php echo htmlspecialchars($dest['name'], ENT_QUOTES); ?>",
    "item": "<?php echo $metaUrl; ?>"
  }]
}
</script>

<div id="content-area" class="flex-1 bg-white min-h-screen">
    <!-- Hero Section - Magazine Style -->
    <div class="relative h-[90vh]">
        <div class="absolute inset-0">
            <img src="<?php echo base_url($dest['image']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>"
                class="w-full h-full object-cover parallax-img">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/40"></div>
        </div>

        <!-- Hero Content -->
        <div class="absolute inset-0 flex items-center">
            <div class="container mx-auto px-6">
                <div class="max-w-4xl">
                    <h1
                        class="text-6xl md:text-8xl lg:text-9xl font-heading font-black text-white mb-6 leading-none drop-shadow-2xl animate-fade-in-up">
                        <?php echo htmlspecialchars($dest['name']); ?>
                    </h1>
                    <p class="text-white/90 text-xl md:text-2xl font-light max-w-2xl leading-relaxed drop-shadow-lg animate-fade-in-up"
                        style="animation-delay: 0.2s">
                        <?php echo htmlspecialchars($dest['country']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Floating Info Card -->
        <div class="absolute bottom-8 right-8 bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-2xl animate-fade-in-up border border-white/20"
            style="animation-delay: 0.4s">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Destination Type</p>
                    <p class="font-bold text-slate-900"><?php echo $dest['type']; ?></p>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-1">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <span
                            class="<?php echo $i < floor($dest['rating']) ? 'text-amber-400' : 'text-gray-300'; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <span class="text-sm font-semibold text-slate-700"><?php echo $dest['rating']; ?></span>
            </div>
        </div>
    </div>

    <!-- Integrated Stats Bar -->
    <div class="bg-white border-y border-slate-200">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat 1: Type -->
                <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-primary group-hover:text-white transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Type</p>
                            <p class="font-bold text-slate-900 text-lg"><?php echo $dest['type']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Stat 2: Best Time -->
                <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-500 group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-amber-600 group-hover:text-white transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Best Time</p>
                            <p class="font-bold text-slate-900 text-lg">All Year</p>
                        </div>
                    </div>
                </div>

                <!-- Stat 3: Rating -->
                <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-500 group-hover:scale-110 transition">
                            <span class="text-3xl group-hover:text-white transition">★</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Rating</p>
                            <p class="font-bold text-slate-900 text-lg"><?php echo $dest['rating']; ?> / 5.0</p>
                        </div>
                    </div>
                </div>

                <!-- Stat 4: Packages -->
                <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-500 group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-green-600 group-hover:text-white transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Packages</p>
                            <p class="font-bold text-slate-900 text-lg">
                                <?php echo count(getPackagesByDestination($dest['id'])); ?> Tours
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-16">
        <div class="max-w-7xl mx-auto">

            <!-- About Section - Full Width -->
            <div class="mb-16">
                <h2 class="text-4xl md:text-5xl font-heading font-bold mb-8 text-slate-900">
                    About <?php echo htmlspecialchars($dest['name']); ?>
                </h2>
                <p class="text-slate-600 leading-relaxed text-xl font-light max-w-4xl">
                    <?php echo $dest['description']; ?>
                </p>

                <!-- Highlights - Horizontal Cards -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-gradient-to-br from-white to-primary/5 p-8 rounded-2xl border border-primary/20 hover:shadow-xl transition group">
                        <div
                            class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-primary group-hover:text-white transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-xl mb-2">Luxury Stays</h3>
                        <p class="text-slate-600 text-sm">Premium accommodations and world-class hospitality</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-white to-primary/5 p-8 rounded-2xl border border-primary/20 hover:shadow-xl transition group">
                        <div
                            class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-primary group-hover:text-white transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-xl mb-2">Sightseeing</h3>
                        <p class="text-slate-600 text-sm">Explore iconic landmarks and hidden gems</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-white to-primary/5 p-8 rounded-2xl border border-primary/20 hover:shadow-xl transition group">
                        <div
                            class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-primary group-hover:text-white transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-xl mb-2">Rich Culture</h3>
                        <p class="text-slate-600 text-sm">Immerse yourself in local traditions and heritage</p>
                    </div>
                </div>
            </div>

            <!-- Interactive Map -->
            <?php if (!empty($dest['map_embed'])): ?>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                    <h2 class="text-3xl font-heading font-bold mb-6 text-slate-900 flex items-center gap-3">
                        <span class="w-10 h-1 bg-primary rounded-full"></span>
                        Location
                    </h2>
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                        <style>
                            .map-container iframe {
                                width: 100%;
                                height: 400px;
                                border: 0;
                                filter: grayscale(0%);
                            }
                        </style>
                        <div class="map-container relative z-0">
                            <?php echo $dest['map_embed']; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Available Packages -->
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-3xl font-heading font-bold text-slate-900">Available Packages</h3>
                    <span
                        class="px-4 py-1.5 rounded-full bg-white border border-slate-200 text-xs text-slate-600 uppercase tracking-widest shadow-sm">
                        <?php echo count($packages); ?> Offers
                    </span>
                </div>

                <?php if (count($packages) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php foreach ($packages as $index => $p): ?>
                            <a href="<?php echo package_url($p['slug']); ?>"
                                class="package-card block bg-white border border-slate-100 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all duration-500 opacity-0 transform translate-y-8"
                                style="transition-delay: <?php echo $index * 100; ?>ms">
                                <div class="relative h-60 overflow-hidden">
                                    <img src="<?php echo base_url($p['image']); ?>"
                                        alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy"
                                        class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-60">
                                    </div>
                                    <div
                                        class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-slate-800 shadow-md">
                                        <?php echo $p['duration']; ?>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h4
                                        class="font-heading font-bold text-xl text-slate-900 mb-2 group-hover:text-primary transition leading-tight">
                                        <?php echo htmlspecialchars($p['title']); ?>
                                    </h4>

                                    <div class="flex flex-wrap gap-2 mb-6">
                                        <?php if (!empty($p['activities'])): ?>
                                            <?php foreach (array_slice($p['activities'], 0, 2) as $act): ?>
                                                <span
                                                    class="text-[10px] text-slate-500 border border-slate-200 px-2 py-1 rounded-md bg-slate-50"><?php echo htmlspecialchars($act); ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex items-end justify-between border-t border-slate-100 pt-4">
                                        <div>
                                            <span
                                                class="text-xs text-slate-400 font-medium block uppercase tracking-wider">Starting
                                                From</span>
                                            <span
                                                class="text-2xl font-bold text-primary">₹<?php echo number_format($p['price']); ?></span>
                                        </div>
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-100 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                            &rarr;
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="bg-white text-center p-12 border border-dashed border-slate-200 rounded-3xl shadow-sm">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-6 text-slate-400">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Coming Soon</h3>
                        <p class="text-slate-500 max-w-md mx-auto mb-8">We are currently curating exclusive experiences
                            for this destination.</p>
                        <a href="<?php echo base_url('pages/contact.php'); ?>"
                            class="bg-primary hover:bg-primary/90 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-primary/30">Request
                            Custom Quote</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            gsap.registerPlugin(ScrollTrigger);

            // Parallax Hero Image
            gsap.to(".parallax-img", {
                yPercent: 20,
                ease: "none",
                scrollTrigger: {
                    trigger: "body",
                    start: "top top",
                    end: "bottom top",
                    scrub: true
                }
            });

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

            // Smooth Card Batch Animations
            animateBatch('.package-card', 80);
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>