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

<!-- Page Header (Luxury) -->
<div class="relative pt-40 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?php echo get_setting('destinations_bg', base_url('assets/images/hero/adventure.png')); ?>" class="w-full h-full object-cover brightness-[0.4]" alt="Destinations Background">
        <div class="absolute inset-0 bg-gradient-to-b from-charcoal/50 to-charcoal"></div>
    </div>
    
    <div class="container mx-auto px-6 relative z-10 text-center">
        <span class="text-secondary font-bold tracking-widest uppercase text-sm mb-4 block">Curated Collection</span>
        <h1 class="text-5xl md:text-7xl font-heading font-bold text-white mb-6">Explore <span class="text-gold">Destinations</span></h1>
        <p class="text-gray-300 max-w-2xl mx-auto text-lg font-light leading-relaxed">
            Discover the most beautiful and exclusive places around the world, handpicked for the discerning traveler.
        </p>
    </div>
</div>

<!-- Main Content -->
<div class="bg-charcoal min-h-screen relative z-10">
    <div class="container mx-auto px-6 py-12">
        <div class="flex flex-col lg:flex-row gap-10">

            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4">
                <div class="glass-form sticky top-32 !p-6 !bg-white/5 border border-white/10 rounded-3xl">
                    <h3 class="text-xl font-heading font-bold mb-6 flex items-center gap-2 text-white">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Refine Search
                    </h3>

                    <form action="<?php echo base_url('pages/destinations.php'); ?>" method="GET">
                        
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="glass-label text-sm ml-1">Search</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                    placeholder="Country or Place..."
                                    class="glass-input w-full !pl-10 !bg-white/5 focus:!border-secondary text-white placeholder-gray-500">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Region -->
                        <div class="mb-6">
                            <label class="glass-label text-sm ml-1 mb-3">Region</label>
                            <div class="space-y-3">
                                <?php 
                                $regions = ['' => 'All Regions', 'International' => 'International', 'Domestic' => 'Domestic'];
                                $currentRegion = $_GET['region'] ?? '';
                                foreach($regions as $val => $label): 
                                ?>
                                <label class="flex items-center group cursor-pointer">
                                    <div class="relative flex items-center">
                                        <input type="radio" name="region" value="<?php echo $val; ?>"
                                            class="peer appearance-none w-5 h-5 border border-white/30 rounded-full checked:border-secondary checked:bg-secondary transition-all"
                                            <?php echo ($currentRegion === $val) ? 'checked' : ''; ?>>
                                        <div class="absolute inset-0 m-auto w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                    </div>
                                    <span class="ml-3 text-gray-400 group-hover:text-white transition-colors text-sm"><?php echo $label; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-8">
                            <label class="glass-label text-sm ml-1 mb-3">Rating</label>
                            <div class="space-y-2">
                                 <?php 
                                 $selectedRating = $_GET['rating'] ?? '';
                                 foreach([4, 3] as $r): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="radio" name="rating" value="<?php echo $r; ?>" 
                                            class="peer appearance-none w-4 h-4 border border-white/30 rounded-full checked:border-secondary checked:bg-secondary transition-all"
                                            <?php echo ($selectedRating == $r) ? 'checked' : ''; ?>>
                                        <span class="ml-2 flex items-center text-gray-400 group-hover:text-white transition-colors text-sm">
                                            <span class="text-gold mr-1"><?php echo str_repeat('★', $r); ?></span> 
                                            <span class="opacity-50"><?php echo str_repeat('☆', 5-$r); ?></span>
                                            <span class="ml-1 text-xs">& Up</span>
                                        </span>
                                    </label>
                                 <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="w-full glass-button hover:bg-secondary/20 shadow-lg">
                            Apply Filters
                        </button>
                        <a href="<?php echo base_url('pages/destinations.php'); ?>" class="block text-center mt-4 text-xs text-gray-500 hover:text-white transition">Reset Filters</a>
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
                    if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

                    $offset = ($currentPage - 1) * $itemsPerPage;
                    $paginatedDestinations = array_slice($filteredDestinations, $offset, $itemsPerPage);

                    if (count($paginatedDestinations) > 0):
                        foreach ($paginatedDestinations as $index => $dest):
                    ?>
                        <!-- Card (GSAP Animated) -->
                        <div class="destination-card opacity-0 transform translate-y-8" style="transition-delay: <?php echo $index * 50; ?>ms">
                            <a href="<?php echo destination_url($dest['slug']); ?>" class="block group relative rounded-3xl overflow-hidden glass-card-dark aspect-[4/5] hover:border-secondary/50">
                                
                                <img src="<?php echo base_url($dest['image']); ?>"
                                     alt="<?php echo htmlspecialchars($dest['name']); ?>"
                                     loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                     onerror="this.src='https://placehold.co/600x800?text=Image+Not+Found'">
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-charcoal/40 to-transparent opacity-90 group-hover:opacity-80 transition-opacity"></div>

                                <!-- Badges -->
                                <div class="absolute top-4 right-4 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white border border-white/10">
                                    <?php echo $dest['type']; ?>
                                </div>
                                <?php if (!empty($dest['is_new'])): ?>
                                    <div class="absolute top-4 left-4 bg-secondary text-white text-[10px] font-bold px-2 py-1 rounded shadow-lg">NEW</div>
                                <?php endif; ?>

                                <!-- Content -->
                                <div class="absolute bottom-0 left-0 w-full p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                    <h3 class="text-2xl font-heading font-bold text-white mb-1 group-hover:text-secondary transition-colors">
                                        <?php echo htmlspecialchars($dest['name']); ?>
                                    </h3>
                                    
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center gap-1 text-gold text-sm">
                                            <span>★</span> <span class="text-gray-200"><?php echo $dest['rating']; ?></span>
                                        </div>
                                        <div class="text-xs text-gray-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                            <?php echo htmlspecialchars($dest['country']); ?>
                                        </div>
                                    </div>

                                    <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-300">
                                        <p class="text-gray-400 text-sm mt-3 line-clamp-2 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                                            <?php echo htmlspecialchars(strip_tags($dest['description'] ?? '')); ?>
                                        </p>
                                        <span class="mt-4 inline-block text-secondary text-sm font-bold border-b border-secondary">View Guide &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; else: ?>
                        <!-- Empty State -->
                        <div class="col-span-3 glass-form text-center py-20">
                            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-white mb-2">No Destinations Found</h3>
                            <p class="text-gray-400">Try adjusting your filters.</p>
                            <a href="<?php echo base_url('pages/destinations.php'); ?>" class="inline-block mt-4 text-secondary hover:text-white transition">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="mt-16 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 text-white hover:bg-white/10 transition">
                                    &larr;
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition <?php echo $i === $currentPage ? 'bg-secondary text-white shadow-lg' : 'bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 text-white hover:bg-white/10 transition">
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
        
        // Staggered Fade In for Cards
        gsap.utils.toArray('.destination-card').forEach(card => {
            gsap.to(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 90%"
                },
                y: 0,
                opacity: 1,
                duration: 0.8,
                ease: "power2.out"
            });
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>