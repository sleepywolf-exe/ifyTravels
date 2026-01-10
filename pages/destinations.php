<?php
$pageTitle = "Destinations";
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[300px] flex items-center justify-center"
    style="background-image: url('<?php echo get_setting('destinations_bg', base_url('assets/images/hero/adventure.png')); ?>');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl font-bold text-white mb-4">Explore Destinations</h1>
        <p class="text-gray-100 max-w-2xl mx-auto">Discover the most beautiful places around the world.</p>
    </div>
</div>

<!-- Filters + Grid -->
<div class="container mx-auto px-6 py-12 flex-1 relative z-10 -mt-10">
    <div class="flex flex-col md:flex-row gap-8">

        <!-- Sidebar Filters -->
        <aside class="w-full md:w-1/4">
            <div
                class="glass-panel p-6 sticky top-24 bg-white/50 backdrop-blur-lg border border-gray-200 rounded-2xl shadow-sm">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2 text-charcoal">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                    Filters
                </h3>

                <!-- Form for PHP filtering -->
                <form action="<?php echo base_url('destinations'); ?>" method="GET">
                    
                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                placeholder="Destination or Country..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:outline-none">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Region -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3 text-gray-700">Region</label>
                        <div class="space-y-3">
                            <label class="flex items-center text-gray-600 hover:text-primary cursor-pointer group transition">
                                <span class="relative flex items-center">
                                    <input type="radio" name="region" value=""
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border border-gray-200 checked:border-primary checked:bg-primary transition-all"
                                        <?php echo (empty($_GET['region'])) ? 'checked' : ''; ?>>
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></span>
                                </span>
                                <span class="ml-3 group-hover:translate-x-1 transition-transform">All Regions</span>
                            </label>
                            <label class="flex items-center text-gray-600 hover:text-primary cursor-pointer group transition">
                                <span class="relative flex items-center">
                                    <input type="radio" name="region" value="International"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border border-gray-200 checked:border-primary checked:bg-primary transition-all"
                                        <?php echo (isset($_GET['region']) && $_GET['region'] === 'International') ? 'checked' : ''; ?>>
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></span>
                                </span>
                                <span class="ml-3 group-hover:translate-x-1 transition-transform">International</span>
                            </label>
                            <label class="flex items-center text-gray-600 hover:text-primary cursor-pointer group transition">
                                <span class="relative flex items-center">
                                    <input type="radio" name="region" value="Domestic"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border border-gray-200 checked:border-primary checked:bg-primary transition-all"
                                        <?php echo (isset($_GET['region']) && $_GET['region'] === 'Domestic') ? 'checked' : ''; ?>>
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></span>
                                </span>
                                <span class="ml-3 group-hover:translate-x-1 transition-transform">Domestic</span>
                            </label>
                        </div>
                    </div>

                    <!-- Minimum Rating -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold mb-3 text-gray-700">Minimum Rating</label>
                        <div class="space-y-2">
                             <?php 
                             $selectedRating = $_GET['rating'] ?? 0;
                             foreach([4, 3] as $r): ?>
                                <label class="flex items-center text-gray-600 cursor-pointer">
                                    <input type="radio" name="rating" value="<?php echo $r; ?>" 
                                        class="text-primary focus:ring-primary h-4 w-4"
                                        <?php echo ($selectedRating == $r) ? 'checked' : ''; ?>>
                                    <span class="ml-2 flex items-center">
                                        <?php echo str_repeat('★', $r); ?><?php echo str_repeat('☆', 5-$r); ?>
                                        <span class="ml-1 text-xs text-gray-400">& Up</span>
                                    </span>
                                </label>
                             <?php endforeach; ?>
                             <label class="flex items-center text-gray-600 cursor-pointer">
                                <input type="radio" name="rating" value="" class="text-primary focus:ring-primary h-4 w-4" <?php echo empty($selectedRating) ? 'checked' : ''; ?>>
                                <span class="ml-2 text-sm">Any Rating</span>
                             </label>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition shadow-lg flex items-center justify-center">
                        Apply Filters
                    </button>
                    <a href="<?php echo base_url('destinations'); ?>" class="block text-center mt-3 text-xs text-gray-500 hover:text-primary transition">Reset All Filters</a>
                </form>
            </div>
        </aside>

        <!-- Grid -->
        <main class="w-full md:w-3/4">
            <div id="destinations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                // Filter Logic
                $filteredDestinations = $destinations;

                // 1. Region Filter
                // 1. Region Filter
                if (!empty($_GET['region'])) {
                    $selectedRegion = $_GET['region'];
                    $filteredDestinations = array_filter($filteredDestinations, function ($d) use ($selectedRegion) {
                        return $d['type'] === $selectedRegion;
                    });
                }

                // 2. Search Filter
                if (!empty($_GET['search'])) {
                    $term = strtolower(trim($_GET['search']));
                    $filteredDestinations = array_filter($filteredDestinations, function ($d) use ($term) {
                        return strpos(strtolower($d['name']), $term) !== false || strpos(strtolower($d['country']), $term) !== false;
                    });
                }

                // 3. Rating Filter
                if (!empty($_GET['rating'])) {
                    $minRate = floatval($_GET['rating']);
                    $filteredDestinations = array_filter($filteredDestinations, function ($d) use ($minRate) {
                        return floatval($d['rating']) >= $minRate;
                    });
                }

                // Pagination Logic (New)
                $itemsPerPage = 9;
                $totalItems = count($filteredDestinations);
                $totalPages = ceil($totalItems / $itemsPerPage);
                $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                if ($currentPage > $totalPages && $totalPages > 0)
                    $currentPage = $totalPages;

                $offset = ($currentPage - 1) * $itemsPerPage;
                $paginatedDestinations = array_slice($filteredDestinations, $offset, $itemsPerPage);

                if (count($paginatedDestinations) > 0):
                    foreach ($paginatedDestinations as $dest):
                        ?>
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                            <a href="<?php echo destination_url($dest['slug']); ?>" class="absolute inset-0 z-10"></a>
                            <div class="block relative h-56 overflow-hidden">
                                <img src="<?php echo base_url($dest['image']); ?>"
                                    alt="<?php echo htmlspecialchars($dest['name']); ?>"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700"
                                    onerror="this.src='https://placehold.co/600x400?text=No+Image'">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-primary shadow-sm">
                                    <?php echo $dest['type']; ?>
                                </div>
                                <?php if (!empty($dest['is_new'])): ?>
                                    <div class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm animate-pulse">
                                        NEW
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-charcoal group-hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($dest['name']); ?>
                                    </h3>
                                    <div class="flex items-center bg-yellow-100 px-2 py-0.5 rounded text-xs text-yellow-700">
                                        <span>★</span>
                                        <span class="ml-1 font-bold"><?php echo $dest['rating']; ?></span>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500 mb-6 line-clamp-3">
                                    <?php echo htmlspecialchars(strip_tags($dest['description'] ?? '')); ?>
                                </p>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <?php echo $dest['country'] ?? 'Explore'; ?>
                                    </span>
                                    <span
                                        class="text-sm font-bold text-primary hover:text-secondary transition-colors flex items-center group/link">
                                        View Details
                                        <svg class="w-4 h-4 ml-1 transform group-hover/link:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                else: ?>
                    <div class="col-span-3 glass-panel p-10 text-center">
                        <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <h3 class="text-xl font-bold text-white mb-2">No Destinations Found</h3>
                        <p class="text-gray-400">Try adjusting your filters to find what you're looking for.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination UI -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <!-- Prev -->
                        <?php if ($currentPage > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>"
                                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <!-- Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                                class="w-10 h-10 flex items-center justify-center rounded-lg font-bold transition <?php echo $i === $currentPage ? 'bg-primary text-white shadow-md' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Next -->
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>"
                                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>