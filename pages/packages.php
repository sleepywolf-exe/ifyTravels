<?php
$pageTitle = "Packages";
include __DIR__ . '/../includes/header.php';

// Search Logic
$search = $_GET['search'] ?? '';
$destId = $_GET['destination'] ?? '';
$durationInput = $_GET['duration'] ?? '';

$filteredPackages = $packages;

// Server-side Filtering
if (!empty($destId)) {
    $filteredPackages = array_filter($filteredPackages, function ($pkg) use ($destId) {
        return isset($pkg['destination_id']) && $pkg['destination_id'] == $destId;
    });
}

if (!empty($durationInput)) {
    $filteredPackages = array_filter($filteredPackages, function ($pkg) use ($durationInput) {
        // Simple fuzzy match for duration (e.g. "5" matches "5 Days")
        return stripos($pkg['duration'], "$durationInput Day") !== false;
    });
}

if (!empty($search)) {
    $search = strtolower(trim($search));
    $filteredPackages = array_filter($filteredPackages, function ($pkg) use ($search) {
        // Search Title
        if (strpos(strtolower($pkg['title']), $search) !== false)
            return true;

        // Search Features
        if (is_array($pkg['features'])) {
            foreach ($pkg['features'] as $f) {
                if (strpos(strtolower($f), $search) !== false)
                    return true;
            }
        }
        return false;
    });
}
?>

<!-- Header (Dynamic) -->

<!-- Header (Dynamic) -->
<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[300px] flex items-center justify-center"
    style="background-image: url('<?php echo get_setting('packages_bg', base_url('assets/images/packages/thailand.jpg')); ?>');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl font-bold text-white mb-4">Exclusive Packages</h1>
        <p class="text-gray-100 max-w-2xl mx-auto">All-inclusive experiences designed for hassle-free travel.</p>
    </div>
</div>

<div class="container mx-auto px-6 py-12 flex-1">
    <!-- Search & Filters -->
    <div class="max-w-xl mx-auto mb-12">
        <form action="" method="GET" class="relative">
            <input type="text" id="package-search-input" name="search"
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                placeholder="Search packages (e.g., Luxury, Bali, 5 Days)..."
                class="w-full pl-12 pr-6 py-4 rounded-full border border-gray-200 shadow-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
            <button type="submit"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </form>
    </div>

    <!-- Results Count -->
    <div id="results-count" class="text-center text-gray-500 mb-6 hidden">
        Found <span id="visible-count" class="font-bold text-primary">0</span> packages
    </div>

    <div id="packages-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($filteredPackages) && count($filteredPackages) > 0): ?>
            <?php foreach ($filteredPackages as $pkg): ?>
                <div class="package-card group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition flex flex-col h-full transform hover:-translate-y-1"
                    data-title="<?php echo htmlspecialchars(strtolower($pkg['title'])); ?>"
                    data-price="<?php echo $pkg['price']; ?>"
                    data-duration="<?php echo htmlspecialchars(strtolower($pkg['duration'])); ?>"
                    data-features="<?php echo htmlspecialchars(strtolower(implode(' ', $pkg['features']))); ?>">

                    <div class="relative h-64 overflow-hidden">
                        <img src="<?php echo base_url($pkg['image']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700"
                            onerror="this.src='https://placehold.co/600x400?text=Image+Not+Found'">

                        <?php if (isset($pkg['isPopular']) && $pkg['isPopular']): ?>
                            <div
                                class="absolute top-4 left-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1">
                                🔥 POPULAR</div>
                        <?php endif; ?>

                        <div
                            class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-xl text-lg font-bold text-charcoal shadow-sm">
                            ₹
                            <?php echo $pkg['price']; ?> <span class="text-xs font-normal text-gray-500">/ person</span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-charcoal group-hover:text-primary transition line-clamp-1">
                                <?php echo htmlspecialchars($pkg['title']); ?>
                            </h3>
                        </div>

                        <div class="flex items-center text-sm text-gray-500 mb-6">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-primary" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo $pkg['duration']; ?>
                            </span>
                        </div>

                        <div class="space-y-2 mb-8 flex-1">
                            <?php foreach (array_slice($pkg['features'], 0, 3) as $f): ?>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    <span class="truncate">
                                        <?php echo htmlspecialchars($f); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <a href="<?php echo package_url($pkg['slug']); ?>"
                            class="block w-full text-center bg-gray-50 hover:bg-primary hover:text-white text-charcoal font-bold py-3.5 rounded-xl transition border border-gray-200 shadow-sm">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div id="no-results-msg" class="col-span-full text-center py-20">
                <p class="text-gray-500 text-xl">No packages found matching your criteria.</p>
                <a href="packages.php" class="mt-4 block text-primary font-bold hover:underline">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- No Results (Client-side) -->
    <div id="js-no-results" class="hidden text-center py-20">
        <p class="text-gray-500 text-xl">No packages found matching "<span id="js-search-term"
                class="font-bold"></span>"</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('package-search-input');
        const grid = document.getElementById('packages-grid');
        const noResultsMsg = document.getElementById('js-no-results');
        const searchTermSpan = document.getElementById('js-search-term');
        const resultsCount = document.getElementById('results-count');
        const visibleCountSpan = document.getElementById('visible-count');

        let debounceTimer;

        // Store original content to restore if search is cleared (optional, or just reload)
        // For simplicity and "futuristic" feel, we'll just keep the API search active or reload if empty.
        // Actually, if empty, we might want to show original PHP rendered content or fetch all.
        // Let's fetch all or reload. Reloading is easiest to restore initial state perfectly.
        // Better: Fetch top packages if empty.

        searchInput.addEventListener('input', function (e) {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();

            debounceTimer = setTimeout(() => {
                if (query.length === 0) {
                    // Reload page to restore original state or clear filters
                    window.location.href = 'packages.php';
                    return;
                }

                // Show loading state
                grid.style.opacity = '0.5';

                fetch(`../services/search_packages.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        grid.innerHTML = ''; // Clear current
                        grid.style.opacity = '1';

                        if (data.length > 0) {
                            noResultsMsg.classList.add('hidden');
                            resultsCount.classList.remove('hidden');
                            visibleCountSpan.textContent = data.length;

                            data.forEach(pkg => {
                                const card = createPackageCard(pkg);
                                grid.appendChild(card);
                                // Trigger reflow for animation
                                void card.offsetWidth;
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            });
                        } else {
                            noResultsMsg.classList.remove('hidden');
                            searchTermSpan.textContent = query;
                            resultsCount.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        grid.style.opacity = '1';
                    });
            }, 300);
        });

        function createPackageCard(pkg) {
            const div = document.createElement('div');
            div.className = 'package-card group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition flex flex-col h-full transform translate-y-4 opacity-0 transition-all duration-500';

            // Features HTML
            let featuresHtml = '';
            if (pkg.features && pkg.features.length) {
                pkg.features.slice(0, 3).forEach(f => {
                    featuresHtml += `
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="truncate">${escapeHtml(f)}</span>
                        </div>`;
                });
            }

            const isPopularHtml = pkg.is_popular ?
                `<div class="absolute top-4 left-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1">🔥 POPULAR</div>` : '';

            div.innerHTML = `
                <div class="relative h-64 overflow-hidden">
                    <img src="${pkg.image}" alt="${escapeHtml(pkg.title)}"
                        class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700"
                        onerror="this.src='https://placehold.co/600x400?text=Image+Not+Found'">
                    ${isPopularHtml}
                    <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-xl text-lg font-bold text-charcoal shadow-sm">
                        ₹ ${pkg.price} <span class="text-xs font-normal text-gray-500">/ person</span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-charcoal group-hover:text-primary transition line-clamp-1">
                            ${escapeHtml(pkg.title)}
                        </h3>
                    </div>

                    <div class="flex items-center text-sm text-gray-500 mb-6">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ${escapeHtml(pkg.duration)}
                        </span>
                    </div>

                    <div class="space-y-2 mb-8 flex-1">
                        ${featuresHtml}
                    </div>

                    <a href="${pkg.url}"
                        class="block w-full text-center bg-gray-50 hover:bg-primary hover:text-white text-charcoal font-bold py-3.5 rounded-xl transition border border-gray-200 shadow-sm">
                        View Details
                    </a>
                </div>
            `;
            return div;
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>