<?php
// Destination Details Page
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php'; // Has getDestinationById() and getPackagesByDestination()

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
    <div id="error-state" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4 pt-32">
        <h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-charcoal mb-2">Destination Not Found</h2>
        <a href="destinations.php" class="bg-primary text-white px-6 py-2 rounded-lg font-bold mt-4">Browse Destinations</a>
    </div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// FIX: Ensure we have the ID for fetching packages, even if we came via slug
$id = $dest['id'];

$pageTitle = $dest['name'];
include __DIR__ . '/../includes/header.php';
$packages = getPackagesByDestination($id);
?>

<div id="content-area" class="flex-1">
    <!-- Hero Section -->
    <div class="relative h-[70vh]">
        <img src="<?php echo base_url($dest['image']); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-charcoal/50 to-transparent flex items-end">
            <div class="container mx-auto px-6 pb-16 text-white">
                <div class="animate-fade-in-up">
                    <span
                        class="bg-primary/90 backdrop-blur text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block shadow-lg">
                        <?php echo $dest['type']; ?>
                    </span>
                    <h1 class="text-6xl md:text-7xl font-heading font-bold mb-4 leading-tight">
                        <?php echo htmlspecialchars($dest['name']); ?>
                    </h1>
                    <div class="flex items-center gap-4 text-white/90 text-lg">
                        <div class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-lg backdrop-blur-sm">
                            <span class="text-yellow-400">★</span>
                            <span class="font-bold"><?php echo $dest['rating']; ?></span>
                        </div>
                        <span>•</span>
                        <span><?php echo $dest['country']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-16 -mt-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-12">

                <!-- About Section -->
                <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl border border-gray-100">
                    <h2 class="text-3xl font-heading font-bold mb-6 text-charcoal flex items-center gap-3">
                        <span class="w-10 h-1 bg-primary rounded-full"></span>
                        About <?php echo htmlspecialchars($dest['name']); ?>
                    </h2>
                    <p class="text-gray-600 leading-loose text-lg font-light">
                        <?php echo $dest['description']; ?>
                    </p>

                    <!-- Highlights (Static Placeholder for now, can be dynamic later) -->
                    <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div
                            class="text-center p-4 bg-gray-50 rounded-xl hover:bg-primary/5 transition group cursor-default">
                            <div
                                class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="font-bold text-gray-700 block text-sm">Top Hotels</span>
                        </div>
                        <div
                            class="text-center p-4 bg-gray-50 rounded-xl hover:bg-primary/5 transition group cursor-default">
                            <div
                                class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-green-600 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="font-bold text-gray-700 block text-sm">Sightseeing</span>
                        </div>
                        <div
                            class="text-center p-4 bg-gray-50 rounded-xl hover:bg-primary/5 transition group cursor-default">
                            <div
                                class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="font-bold text-gray-700 block text-sm">Culture</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Map -->
                <?php if (!empty($dest['map_embed'])): ?>
                    <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 overflow-hidden">
                        <h2 class="text-3xl font-heading font-bold mb-6 text-charcoal flex items-center gap-3">
                            <span class="w-10 h-1 bg-primary rounded-full"></span>
                            Location
                        </h2>
                        <div class="aspect-w-16 aspect-h-9 rounded-2xl overflow-hidden border border-gray-200">
                            <!-- Helper for responsive iframe -->
                             <style>
                                .map-container iframe { width: 100%; height: 400px; border: 0; }
                             </style>
                             <div class="map-container">
                                 <?php echo $dest['map_embed']; ?>
                             </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Available Packages -->
                <div>
                    <h3 class="text-3xl font-heading font-bold mb-8 text-charcoal flex items-center justify-between">
                        Available Packages
                        <span
                            class="text-sm font-normal text-gray-500 bg-gray-100 px-3 py-1 rounded-full"><?php echo count($packages); ?>
                            Offers</span>
                    </h3>

                    <?php if (count($packages) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php foreach ($packages as $p): ?>
                                <div
                                    class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="relative h-56 overflow-hidden">
                                        <img src="<?php echo base_url($p['image']); ?>"
                                            class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                        <div
                                            class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-primary shadow-sm">
                                            <?php echo $p['duration']; ?>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h4
                                            class="font-bold text-xl text-charcoal mb-3 group-hover:text-primary transition leading-tight">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </h4>
                                        <div class="flex flex-wrap gap-2 mb-6">
                                            <?php foreach (array_slice($p['features'], 0, 3) as $f): ?>
                                                <span
                                                    class="text-xs bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-100">
                                                    <?php echo htmlspecialchars($f); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="flex items-end justify-between border-t pt-4">
                                            <div>
                                                <span
                                                    class="text-xs text-gray-400 font-medium block uppercase tracking-wider">Starting
                                                    From</span>
                                                <span
                                                    class="text-2xl font-bold text-primary">₹<?php echo number_format($p['price']); ?></span>
                                            </div>
                                            <a href="<?php echo package_url($p['slug']); ?>"
                                                class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-teal-700 transition shadow-lg group-hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State Card -->
                        <div
                            class="bg-gradient-to-br from-primary/5 to-blue-50/50 rounded-2xl p-12 text-center border border-dashed border-primary/20">
                            <div
                                class="w-20 h-20 bg-white rounded-full mx-auto flex items-center justify-center shadow-md mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-charcoal mb-2">No Packages Listed Yet</h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-8">We are crafting the perfect itinerary for this
                                destination. Want a custom plan?</p>
                            <a href="contact.php"
                                class="inline-block bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-teal-700 transition shadow-lg hover:shadow-primary/30">
                                Request Custom Quote
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Column: Sidebar -->
            <aside class="w-full">
                <div class="sticky top-24 space-y-8">
                    <!-- Quick Facts Card -->
                    <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
                        <h3 class="font-heading font-bold text-xl mb-6 text-charcoal flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quick Facts
                        </h3>
                        <ul class="space-y-5 text-sm">
                            <li
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 group border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary/10 transition shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span>Type</span>
                                </div>
                                <span class="font-bold text-charcoal text-right"><?php echo $dest['type']; ?></span>
                            </li>
                            <li
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 group border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary/10 transition shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span>Best Time</span>
                                </div>
                                <span class="font-bold text-charcoal text-right">All Year</span>
                            </li>
                            <li
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 group border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary/10 transition shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <span>Currency</span>
                                </div>
                                <span class="font-bold text-charcoal text-right">Local / USD</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Need Help CTA -->
                    <div
                        class="bg-gradient-to-br from-charcoal to-gray-800 rounded-3xl p-8 text-white text-center shadow-xl">
                        <h4 class="font-bold text-xl mb-3">Planning a Trip?</h4>
                        <p class="text-gray-300 text-sm mb-6">Our travel experts can help you craft the perfect
                            itinerary.</p>

                        <!-- Desktop: Open Modal -->
                        <button
                            onclick="openLeadModal(null, 'Trip Inquiry: <?php echo htmlspecialchars($dest['name']); ?>')"
                            class="hidden md:block w-full bg-white text-charcoal font-bold py-3 rounded-xl hover:bg-gray-100 transition">
                            Contact Expert
                        </button>

                        <!-- Mobile: Call Directly -->
                        <a href="tel:<?php echo get_setting('contact_phone', '+919999779870'); ?>"
                            class="md:hidden block w-full bg-white text-charcoal font-bold py-3 rounded-xl hover:bg-gray-100 transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            Call Expert
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>