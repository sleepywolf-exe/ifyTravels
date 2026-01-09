<?php
$pageTitle = "Packages";
include __DIR__ . '/../includes/header.php';

// --- 1. Filter Logic ---

// Get all packages first (from loader.php included in header -> functions -> loader)
// Note: functions.php usually includes loader.php. If not, we ensure it.
if (!isset($packages)) {
    include_once __DIR__ . '/../data/loader.php';
}

// 1.1 Helpers for filter generation
$allActivities = [];
$allThemes = [];
foreach ($packages as $p) {
    if (!empty($p['activities'])) {
        $acts = is_string($p['activities']) ? json_decode($p['activities'], true) : $p['activities'];
        if (is_array($acts))
            $allActivities = array_merge($allActivities, $acts);
    }
    if (!empty($p['themes'])) {
        $thms = is_string($p['themes']) ? json_decode($p['themes'], true) : $p['themes'];
        if (is_array($thms))
            $allThemes = array_merge($allThemes, $thms);
    }
}
$allActivities = array_unique($allActivities);
sort($allActivities);
$allThemes = array_unique($allThemes);
sort($allThemes);

// 1.2 Capture Inputs
$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$selectedDurations = $_GET['duration_filter'] ?? []; // Array
$selectedActivities = $_GET['activities'] ?? []; // Array
$selectedThemes = $_GET['themes'] ?? []; // Array

// 1.3 Apply Filters
$filteredPackages = array_filter($packages, function ($pkg) use ($search, $minPrice, $maxPrice, $selectedDurations, $selectedActivities, $selectedThemes) {

    // Search
    if (!empty($search)) {
        $term = strtolower(trim($search));
        $inTitle = strpos(strtolower($pkg['title']), $term) !== false;
        $inDest = false; // You could search destination name if linked
        if (!$inTitle)
            return false;
    }

    // Price
    if ($minPrice !== '' && $pkg['price'] < $minPrice)
        return false;
    if ($maxPrice !== '' && $pkg['price'] > $maxPrice)
        return false;

    // Duration (Logic: Parse "5 Days" -> 5)
    if (!empty($selectedDurations)) {
        $dVal = intval($pkg['duration']); // "5 Days" -> 5
        $match = false;
        foreach ($selectedDurations as $range) {
            if ($range === 'short' && $dVal < 3)
                $match = true;
            if ($range === 'medium' && $dVal >= 3 && $dVal <= 5)
                $match = true;
            if ($range === 'long' && $dVal > 5 && $dVal <= 7)
                $match = true;
            if ($range === 'extended' && $dVal > 7)
                $match = true;
        }
        if (!$match)
            return false;
    }

    // Activities (OR logic: has at least one selected activity)
    if (!empty($selectedActivities)) {
        $pkgActs = is_string($pkg['activities']) ? json_decode($pkg['activities'], true) : $pkg['activities'];
        if (empty($pkgActs) || !array_intersect($selectedActivities, $pkgActs))
            return false;
    }

    // Themes (OR logic)
    if (!empty($selectedThemes)) {
        $pkgThemes = is_string($pkg['themes']) ? json_decode($pkg['themes'], true) : $pkg['themes'];
        if (empty($pkgThemes) || !array_intersect($selectedThemes, $pkgThemes))
            return false;
    }

    return true;
});

// --- 2. Pagination Logic ---
$itemsPerPage = 6;
$totalItems = count($filteredPackages);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
if ($currentPage > $totalPages && $totalPages > 0)
    $currentPage = $totalPages;

$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedPackages = array_slice($filteredPackages, $offset, $itemsPerPage);
?>

<!-- Header -->
<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[300px] flex items-center justify-center"
    style="background-image: url('<?php echo get_setting('packages_bg', base_url('assets/images/packages/thailand.jpg')); ?>');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl font-bold text-white mb-4">Exclusive Packages</h1>
        <p class="text-gray-100 max-w-2xl mx-auto">Find your perfect getaway with our curated selection.</p>
    </div>
</div>

<div class="container mx-auto px-6 py-12 flex-1">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Sidebar Filters -->
        <aside class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-charcoal">Filters</h3>
                    <a href="packages.php" class="text-xs text-primary font-bold hover:underline">Reset All</a>
                </div>

                <form action="packages.php" method="GET" id="filterForm">
                    <!-- Search Hidden -->
                    <?php if (!empty($search)): ?><input type="hidden" name="search"
                            value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>

                    <!-- Price -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Price Range (₹)</h4>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>"
                                placeholder="Min" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>"
                                placeholder="Max" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Duration</h4>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2 text-sm text-gray-600">
                                <input type="checkbox" name="duration_filter[]" value="short" <?php echo in_array('short', $selectedDurations) ? 'checked' : ''; ?>
                                    class="rounded text-primary focus:ring-primary">
                                <span>Short Break (< 3 Days)</span>
                            </label>
                            <label class="flex items-center space-x-2 text-sm text-gray-600">
                                <input type="checkbox" name="duration_filter[]" value="medium" <?php echo in_array('medium', $selectedDurations) ? 'checked' : ''; ?>
                                    class="rounded text-primary focus:ring-primary">
                                <span>3 - 5 Days</span>
                            </label>
                            <label class="flex items-center space-x-2 text-sm text-gray-600">
                                <input type="checkbox" name="duration_filter[]" value="long" <?php echo in_array('long', $selectedDurations) ? 'checked' : ''; ?>
                                    class="rounded text-primary focus:ring-primary">
                                <span>5 - 7 Days</span>
                            </label>
                            <label class="flex items-center space-x-2 text-sm text-gray-600">
                                <input type="checkbox" name="duration_filter[]" value="extended" <?php echo in_array('extended', $selectedDurations) ? 'checked' : ''; ?>
                                    class="rounded text-primary focus:ring-primary">
                                <span>Extended Trip (> 7 Days)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Themes -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Themes</h4>
                        <div class="max-h-40 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                            <?php foreach ($allThemes as $theme): ?>
                                <label class="flex items-center space-x-2 text-sm text-gray-600">
                                    <input type="checkbox" name="themes[]" value="<?php echo htmlspecialchars($theme); ?>"
                                        <?php echo in_array($theme, $selectedThemes) ? 'checked' : ''; ?>
                                        class="rounded text-primary">
                                    <span><?php echo htmlspecialchars($theme); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Activities -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Activities</h4>
                        <div class="max-h-40 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                            <?php foreach ($allActivities as $act): ?>
                                <label class="flex items-center space-x-2 text-sm text-gray-600">
                                    <input type="checkbox" name="activities[]" value="<?php echo htmlspecialchars($act); ?>"
                                        <?php echo in_array($act, $selectedActivities) ? 'checked' : ''; ?>
                                        class="rounded text-primary">
                                    <span><?php echo htmlspecialchars($act); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition">Apply
                        Filters</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:col-span-3">
            <!-- Search Bar -->
            <div class="mb-8">
                <form action="packages.php" method="GET" class="relative">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search packages by name..."
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-primary outline-none">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <!-- Preserve other filters -->
                    <?php if ($minPrice): ?><input type="hidden" name="min_price"
                            value="<?php echo $minPrice; ?>"><?php endif; ?>
                    <?php if ($maxPrice): ?><input type="hidden" name="max_price"
                            value="<?php echo $maxPrice; ?>"><?php endif; ?>
                </form>
            </div>

            <!-- Stats -->
            <p class="text-gray-500 mb-6 text-sm">Showing <strong><?php echo count($paginatedPackages); ?></strong> of
                <strong><?php echo $totalItems; ?></strong> packages</p>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <?php if (!empty($paginatedPackages)): ?>
                    <?php foreach ($paginatedPackages as $pkg): ?>
                        <div
                            class="package-card group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition flex flex-col h-full hover:-translate-y-1">
                            <div class="relative h-56 overflow-hidden">
                                <img src="<?php echo base_url($pkg['image']); ?>"
                                    alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700"
                                    onerror="this.src='https://placehold.co/600x400?text=Image+Not+Found'">
                                <?php if ($pkg['isPopular']): ?>
                                    <div
                                        class="absolute top-4 left-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1">
                                        🔥 POPULAR
                                    </div>
                                <?php endif; ?>
                                <div
                                    class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-lg text-base font-bold text-charcoal shadow-sm">
                                    ₹ <?php echo number_format($pkg['price']); ?>
                                </div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-charcoal group-hover:text-primary transition mb-2">
                                    <?php echo htmlspecialchars($pkg['title']); ?>
                                </h3>
                                <div class="flex items-center text-xs text-gray-500 mb-4">
                                    <svg class="w-4 h-4 mr-1 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo $pkg['duration']; ?>
                                </div>
                                <div class="space-y-1 mb-6 flex-1">
                                    <?php if (!empty($pkg['features'])):
                                        foreach (array_slice($pkg['features'], 0, 3) as $f): ?>
                                            <div class="flex items-center text-xs text-gray-600">
                                                <svg class="w-3.5 h-3.5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="truncate"><?php echo htmlspecialchars($f); ?></span>
                                            </div>
                                        <?php endforeach; endif; ?>
                                </div>
                                <a href="<?php echo package_url($pkg['slug']); ?>"
                                    class="block w-full text-center bg-gray-50 hover:bg-primary hover:text-white text-charcoal font-bold py-3 rounded-lg transition border border-gray-200">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-20">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-gray-500 text-lg">No packages found matching your criteria.</p>
                        <a href="packages.php" class="mt-2 text-primary font-bold hover:underline">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
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

<style>
    /* Custom Scrollbar for filter lists */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 2px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #bbb;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
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