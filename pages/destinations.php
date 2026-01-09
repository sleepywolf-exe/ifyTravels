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
                <form action="" method="GET">
                    <div class="mb-8">
                        <label class="block text-sm font-semibold mb-3 text-gray-700">Region</label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center text-gray-600 hover:text-primary cursor-pointer group transition">
                                <span class="relative flex items-center">
                                    <input type="checkbox" name="region[]" value="International"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 checked:border-primary checked:bg-primary transition-all">
                                    <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"
                                        viewBox="0 0 14 14" fill="none">
                                        <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="ml-3 group-hover:translate-x-1 transition-transform">International</span>
                            </label>
                            <label
                                class="flex items-center text-gray-600 hover:text-primary cursor-pointer group transition">
                                <span class="relative flex items-center">
                                    <input type="checkbox" name="region[]" value="Domestic"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 checked:border-primary checked:bg-primary transition-all">
                                    <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"
                                        viewBox="0 0 14 14" fill="none">
                                        <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="ml-3 group-hover:translate-x-1 transition-transform">Domestic</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-teal-700 transition shadow-lg flex items-center justify-center">
                        Apply Filters
                    </button>
                </form>
            </div>
        </aside>

        <!-- Grid -->
        <main class="w-full md:w-3/4">
            <div id="destinations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                // Filter Logic
                $filteredDestinations = $destinations;
                if (isset($_GET['region'])) {
                    $regions = is_array($_GET['region']) ? $_GET['region'] : [$_GET['region']]; // Handle single value too
                    if (!empty($regions)) {
                        $filteredDestinations = array_filter($destinations, function ($d) use ($regions) {
                            return in_array($d['type'], $regions);
                        });
                    }
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
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden">
                            <a href="<?php echo destination_url($dest['slug']); ?>" class="block relative h-56 overflow-hidden">
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
                            </a>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-charcoal group-hover:text-primary transition-colors">
                                        <a href="<?php echo destination_url($dest['slug']); ?>">
                                            <?php echo htmlspecialchars($dest['name']); ?>
                                        </a>
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
                                    <a href="<?php echo destination_url($dest['slug']); ?>"
                                        class="text-sm font-bold text-primary hover:text-secondary transition-colors flex items-center group/link">
                                        View Details
                                        <svg class="w-4 h-4 ml-1 transform group-hover/link:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
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