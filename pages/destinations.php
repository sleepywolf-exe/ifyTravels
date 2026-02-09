<?php
$pageTitle = "Destinations";
include __DIR__ . '/../includes/header.php';
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Destinations",
  "description": "Discover top travel destinations around the world.",
  "url": "<?php echo base_url('destinations'); ?>",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      <?php
      // Placeholder - list populated below in grid
      ?>
    ]
  }
}
</script>

<!-- Breadcrumb Schema -->
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
    "item": "<?php echo base_url('destinations'); ?>"
  }]
}
</script>

<!-- Hero Section with Background Image -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo get_setting('destinations_bg', 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80&w=2000'); ?>"
            class="w-full h-full object-cover object-center brightness-[0.40]" alt="Destinations Background">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-black/70"></div>
    </div>

    <!-- Massive Background Text -->
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full text-center pointer-events-none select-none z-0">
        <h2
            class="text-[6rem] md:text-[24rem] font-black text-white opacity-[0.08] leading-none tracking-tighter uppercase font-heading">
            DISCOVER
        </h2>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-6 relative z-10 text-center pt-32 pb-20">
        <!-- Header Content -->
        <div class="max-w-4xl mx-auto">
            <span
                class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-bold tracking-widest uppercase text-sm rounded-full mb-6">
                Curated Collection
            </span>
            <h1 class="text-4xl md:text-7xl font-heading font-bold text-white mb-6 drop-shadow-2xl">
                Explore
                <span class="text-primary relative inline-block">
                    Destinations
                </span>
            </h1>
            <p
                class="text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-2xl mx-auto drop-shadow-lg mb-6">
                Discover the most beautiful and exclusive places around the world, handpicked for discerning travelers.
            </p>

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm justify-center">
                <a href="<?php echo base_url(); ?>" class="text-white/70 hover:text-white transition-colors">Home</a>
                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white font-semibold">Destinations</span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="bg-white min-h-screen relative z-10">
    <div class="container mx-auto px-6 py-16">
        <div class="flex flex-col lg:flex-row gap-10">

            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4">
                <div class="sticky top-32 p-6 bg-white border border-slate-200 rounded-3xl shadow-creative">
                    <h3 class="text-xl font-heading font-bold mb-6 flex items-center gap-2 text-slate-900">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        Refine Search
                    </h3>

                    <form action="<?php echo base_url('destinations'); ?>" method="GET">

                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 ml-1 mb-2">Search</label>
                            <div class="relative">
                                <input type="text" name="search"
                                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                    placeholder="Country or Place..."
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-slate-800 placeholder-slate-400 transition-all">
                                <button type="submit"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors p-1 z-10 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Region -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 ml-1 mb-3">Region</label>
                            <div class="space-y-3">
                                <?php
                                $regions = ['' => 'All Regions', 'International' => 'International', 'Domestic' => 'Domestic'];
                                $currentRegion = $_GET['region'] ?? '';
                                foreach ($regions as $val => $label):
                                    ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input type="radio" name="region" value="<?php echo $val; ?>"
                                                class="peer appearance-none w-5 h-5 border border-slate-300 rounded-full checked:border-primary checked:bg-primary transition-all"
                                                <?php echo ($currentRegion === $val) ? 'checked' : ''; ?>>
                                            <div
                                                class="absolute inset-0 m-auto w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity">
                                            </div>
                                        </div>
                                        <span
                                            class="ml-3 text-slate-600 group-hover:text-primary transition-colors text-sm"><?php echo $label; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 ml-1 mb-3">Rating</label>
                            <div class="space-y-2">
                                <?php
                                $selectedRating = $_GET['rating'] ?? '';
                                foreach ([4, 3] as $r): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="radio" name="rating" value="<?php echo $r; ?>"
                                            class="peer appearance-none w-4 h-4 border border-slate-300 rounded-full checked:border-primary checked:bg-primary transition-all"
                                            <?php echo ($selectedRating == $r) ? 'checked' : ''; ?>>
                                        <span
                                            class="ml-2 flex items-center text-slate-600 group-hover:text-primary transition-colors text-sm">
                                            <span class="text-yellow-500 mr-1"><?php echo str_repeat('★', $r); ?></span>
                                            <span
                                                class="opacity-30 text-slate-400"><?php echo str_repeat('☆', 5 - $r); ?></span>
                                            <span class="ml-1 text-xs">& Up</span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-creative hover:shadow-creative-hover transition-all duration-300">
                            Apply Filters
                        </button>
                        <a href="<?php echo base_url('destinations'); ?>"
                            class="block text-center mt-4 text-xs text-slate-500 hover:text-primary transition">Reset
                            Filters</a>
                    </form>
                </div>
            </aside>

            <!-- Results Grid -->
            <main class="w-full lg:w-3/4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    // Logic - Preserved from original
                    $filteredDestinations = $destinations;

                    if (!empty($_GET['region'])) {
                        $selectedRegion = $_GET['region'];
                        $filteredDestinations = array_filter($filteredDestinations, fn($d) => $d['type'] === $selectedRegion);
                    }

                    if (!empty($_GET['search'])) {
                        $term = strtolower(trim($_GET['search']));
                        $filteredDestinations = array_filter($filteredDestinations, function ($d) use ($term) {
                            return strpos(strtolower($d['name']), $term) !== false || strpos(strtolower($d['country']), $term) !== false;
                        });
                    }

                    if (!empty($_GET['rating'])) {
                        $minRate = floatval($_GET['rating']);
                        $filteredDestinations = array_filter($filteredDestinations, fn($d) => floatval($d['rating']) >= $minRate);
                    }

                    // Pagination
                    $itemsPerPage = 9;
                    $totalItems = count($filteredDestinations);
                    $totalPages = ceil($totalItems / $itemsPerPage);
                    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                    if ($currentPage > $totalPages && $totalPages > 0)
                        $currentPage = $totalPages;

                    $offset = ($currentPage - 1) * $itemsPerPage;
                    $paginatedDestinations = array_slice($filteredDestinations, $offset, $itemsPerPage);

                    if (count($paginatedDestinations) > 0):
                        foreach ($paginatedDestinations as $index => $dest):
                            // ANIMATION LOGIC: First 2 are static, rest animate
                            $animClass = ($index >= 2) ? 'destination-card-animate' : '';
                            ?>
                            <!-- Card (Premium UX) -->
                            <div class="destination-card <?php echo $animClass; ?>">
                                <a href="<?php echo destination_url($dest['slug']); ?>"
                                    class="block group relative rounded-3xl overflow-hidden bg-slate-900 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 ease-out aspect-[4/5]">

                                    <!-- Image (Scale Effect) -->
                                    <img src="<?php echo base_url($dest['image']); ?>"
                                        alt="<?php echo htmlspecialchars($dest['name']); ?>"
                                        loading="<?php echo $index < 2 ? 'eager' : 'lazy'; ?>"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110 opacity-90 group-hover:opacity-100"
                                        onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'">

                                    <!-- Gradient Overlay (Darker on Hover) -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-input from-black/80 via-black/20 to-transparent opacity-60 group-hover:opacity-90 transition-opacity duration-300">
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-hover:opacity-95 transition-all duration-500">
                                    </div>

                                    <!-- Badge -->
                                    <div
                                        class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-slate-800 shadow-sm z-20">
                                        <?php echo $dest['type']; ?>
                                    </div>

                                    <!-- Content Container -->
                                    <div class="absolute bottom-0 left-0 w-full p-6 flex flex-col justify-end h-full z-20">

                                        <!-- Initial View (Title & Rating) -->
                                        <div class="transform transition-transform duration-500 group-hover:-translate-y-2">
                                            <h3 class="text-3xl font-heading font-bold text-white mb-1 drop-shadow-md">
                                                <?php echo htmlspecialchars($dest['name']); ?>
                                            </h3>

                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex text-yellow-500 text-sm">
                                                    <?php
                                                    $rating = floatval($dest['rating']);
                                                    for ($i = 0; $i < 5; $i++)
                                                        echo ($i < floor($rating)) ? '★' : '☆';
                                                    ?>
                                                </div>
                                                <span class="text-white font-bold"><?php echo $dest['rating']; ?></span>
                                            </div>

                                            <div class="flex items-center gap-1 text-slate-300 text-sm font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($dest['country']); ?>
                                            </div>
                                        </div>

                                        <!-- Hidden Description (Reveals on Hover) -->
                                        <div
                                            class="grid grid-rows-[0fr] group-hover:grid-rows-[1fr] transition-all duration-500 ease-out">
                                            <!-- CSS Grid Trick for height transition -->
                                            <div class="overflow-hidden">
                                                <div
                                                    class="pt-4 border-t border-white/20 mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-700 delay-100">
                                                    <p class="text-slate-200 text-sm leading-relaxed line-clamp-2 mb-4">
                                                        <?php echo htmlspecialchars(strip_tags($dest['description'] ?? 'Experience the beauty of ' . $dest['name'])); ?>
                                                    </p>
                                                    <span
                                                        class="inline-flex items-center text-white font-bold border-b border-white pb-0.5 hover:text-secondary hover:border-secondary transition-colors text-sm">
                                                        View Guide <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; else: ?>
                        <!-- Empty State -->
                        <div class="col-span-3 text-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm">
                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">No Destinations Found</h3>
                            <p class="text-slate-500">Try adjusting your filters.</p>
                            <a href="<?php echo base_url('destinations'); ?>"
                                class="inline-block mt-4 text-primary font-bold hover:underline transition">Clear
                                Filters</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="mt-16 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-primary transition shadow-sm">
                                    &larr;
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition <?php echo $i === $currentPage ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white border border-slate-200 text-slate-600 hover:text-primary hover:bg-slate-50 shadow-sm'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-primary transition shadow-sm">
                                    &rarr;
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<!-- Scripts for Animations -->
<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        gsap.registerPlugin(ScrollTrigger);

        // Robust Batch Animation for Grid
        const initBatchAnimation = (selector) => {
            const elements = gsap.utils.toArray(selector);
            if (elements.length === 0) return;

            // Set initial state via GSAP to ensure no flash
            gsap.set(elements, { y: 50, opacity: 0 });

            ScrollTrigger.batch(selector, {
                start: "top 90%",
                onEnter: batch => gsap.to(batch, {
                    opacity: 1,
                    y: 0,
                    stagger: 0.1,
                    duration: 0.6,
                    ease: "power2.out",
                    overwrite: true
                }),
                onEnterBack: batch => gsap.to(batch, {
                    opacity: 1,
                    y: 0,
                    stagger: 0.1,
                    overwrite: true
                }),
                // Optional: onLeave logic could be added here
            });
        };

        // Parallax Hero
        gsap.to(".parallax-bg", {
            yPercent: 30,
            ease: "none",
            scrollTrigger: {
                trigger: "body",
                start: "top top",
                end: "bottom top",
                scrub: true
            }
        });

        // Smooth Card Batch Animations
        initBatchAnimation('.destination-card-animate');
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>