<?php
$pageTitle = "Packages";
include __DIR__ . '/../includes/header.php';

// --- 1. Filter Logic (Preserved) ---
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
$selectedDurations = $_GET['duration_filter'] ?? [];
$selectedActivities = $_GET['activities'] ?? [];
$selectedThemes = $_GET['themes'] ?? [];
$selectedRegion = $_GET['region_filter'] ?? '';
$destinationFilter = $_GET['destination'] ?? '';

// 1.3 Apply Filters
$filteredPackages = array_filter($packages, function ($pkg) use ($search, $minPrice, $maxPrice, $selectedDurations, $selectedActivities, $selectedThemes, $selectedRegion, $destinationFilter) {
    if (!empty($destinationFilter) && $pkg['destinationId'] != $destinationFilter)
        return false;

    if (!empty($selectedRegion)) {
        $dest = getDestinationById($pkg['destinationId']);
        if (!$dest || $dest['type'] !== $selectedRegion)
            return false;
    }

    if (!empty($search)) {
        $term = strtolower(trim($search));
        if (strpos(strtolower($pkg['title']), $term) === false)
            return false;
    }

    if ($minPrice !== '' && $pkg['price'] < $minPrice)
        return false;
    if ($maxPrice !== '' && $pkg['price'] > $maxPrice)
        return false;

    if (!empty($selectedDurations)) {
        $dVal = intval($pkg['duration']);
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

    if (!empty($selectedActivities)) {
        $pkgActs = is_string($pkg['activities']) ? json_decode($pkg['activities'], true) : $pkg['activities'];
        if (empty($pkgActs) || !array_intersect($selectedActivities, $pkgActs))
            return false;
    }

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

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Travel Packages",
  "description": "Explore our exclusive travel packages and holiday deals.",
  "url": "<?php echo base_url('packages'); ?>",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      <?php
      $pos = 1;
      $last = count($paginatedPackages);
      foreach ($paginatedPackages as $pkg) {
          echo '{';
          echo '"@type": "ListItem",';
          echo '"position": ' . $pos . ',';
          echo '"url": "' . package_url($pkg['slug']) . '"';
          echo '}' . ($pos < $last ? ',' : '');
          $pos++;
      }
      ?>
    ]
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
  }]
}
</script>

<!-- Hero Section with Background Image -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo get_setting('packages_bg', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=2000'); ?>"
            class="w-full h-full object-cover object-center brightness-[0.40]" alt="Packages Background">
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
                Hand-Crafted Journeys
            </span>
            <h1 class="text-4xl md:text-7xl font-heading font-bold text-white mb-6 drop-shadow-2xl">
                Exclusive
                <span class="text-primary relative inline-block">
                    Packages
                </span>
            </h1>
            <p
                class="text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-2xl mx-auto drop-shadow-lg mb-6">
                Find your perfect getaway with our curated selection of premium travel experiences.
            </p>

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm justify-center">
                <a href="<?php echo base_url(); ?>" class="text-white/70 hover:text-white transition-colors">Home</a>
                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white font-semibold">Packages</span>
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
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900">Filters</h3>
                        <a href="<?php echo base_url('pages/packages.php'); ?>"
                            class="text-xs text-primary font-bold hover:underline transition">Reset</a>
                    </div>

                    <form action="<?php echo base_url('pages/packages.php'); ?>" method="GET" id="filterForm">
                        <?php if (!empty($search)): ?><input type="hidden" name="search"
                                value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>

                        <!-- Region -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Region</h4>
                            <div class="space-y-2">
                                <?php foreach (['' => 'All Regions', 'International' => 'International', 'Domestic' => 'Domestic'] as $val => $label): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input type="radio" name="region_filter" value="<?php echo $val; ?>"
                                                class="peer appearance-none w-4 h-4 border border-slate-300 rounded-full checked:border-primary checked:bg-primary transition-all"
                                                <?php echo ($selectedRegion == $val) ? 'checked' : ''; ?>>
                                            <div
                                                class="absolute inset-0 m-auto w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity">
                                            </div>
                                        </div>
                                        <span
                                            class="ml-3 text-sm text-slate-600 group-hover:text-primary transition-colors"><?php echo $label; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Price Range (₹)
                            </h4>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>"
                                    placeholder="Min"
                                    class="w-1/2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-primary text-slate-800">
                                <span class="text-slate-400">-</span>
                                <input type="number" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>"
                                    placeholder="Max"
                                    class="w-1/2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-primary text-slate-800">
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Duration</h4>
                            <div class="space-y-2">
                                <?php
                                $durations = [
                                    'short' => 'Short Break (< 3 Days)',
                                    'medium' => '3 - 5 Days',
                                    'long' => '5 - 7 Days',
                                    'extended' => 'Extended Trip (> 7 Days)'
                                ];
                                foreach ($durations as $key => $label): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" name="duration_filter[]" value="<?php echo $key; ?>"
                                            class="peer appearance-none w-4 h-4 border border-slate-300 rounded checked:bg-primary checked:border-primary transition-all"
                                            <?php echo in_array($key, $selectedDurations) ? 'checked' : ''; ?>>
                                        <span
                                            class="ml-3 text-sm text-slate-600 group-hover:text-primary transition-colors"><?php echo $label; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Themes -->
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Themes</h4>
                            <div class="max-h-40 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                                <?php foreach ($allThemes as $theme): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" name="themes[]"
                                            value="<?php echo htmlspecialchars($theme); ?>"
                                            class="peer appearance-none w-4 h-4 border border-slate-300 rounded checked:bg-primary checked:border-primary transition-all"
                                            <?php echo in_array($theme, $selectedThemes) ? 'checked' : ''; ?>>
                                        <span
                                            class="ml-3 text-sm text-slate-600 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($theme); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Activities -->
                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Activities</h4>
                            <div class="max-h-40 overflow-y-auto space-y-2 custom-scrollbar pr-2">
                                <?php foreach ($allActivities as $act): ?>
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" name="activities[]"
                                            value="<?php echo htmlspecialchars($act); ?>"
                                            class="peer appearance-none w-4 h-4 border border-slate-300 rounded checked:bg-primary checked:border-primary transition-all"
                                            <?php echo in_array($act, $selectedActivities) ? 'checked' : ''; ?>>
                                        <span
                                            class="ml-3 text-sm text-slate-600 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($act); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-creative hover:shadow-creative-hover hover:-translate-y-0.5 transition-all duration-300 magnetic-btn">Apply
                            Filters</button>
                    </form>
                </div>
            </aside>

            <!-- Results Grid -->
            <main class="w-full lg:w-3/4">
                <!-- Search Bar -->
                <div class="mb-8 relative">
                    <form action="<?php echo base_url('pages/packages.php'); ?>" method="GET">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search packages..."
                            class="w-full pl-12 py-4 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-slate-800 placeholder-slate-400 shadow-sm transition-all">
                        <button type="submit"
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors p-1 z-10 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <!-- Hidden inputs to preserve filters -->
                        <?php if ($minPrice): ?><input type="hidden" name="min_price"
                                value="<?php echo $minPrice; ?>"><?php endif; ?>
                        <?php if ($maxPrice): ?><input type="hidden" name="max_price"
                                value="<?php echo $maxPrice; ?>"><?php endif; ?>
                    </form>
                </div>

                <p class="text-slate-500 mb-6 text-sm">Showing <strong><?php echo count($paginatedPackages); ?></strong>
                    of <strong><?php echo $totalItems; ?></strong> packages</p>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php if (!empty($paginatedPackages)): ?>
                        <?php foreach ($paginatedPackages as $index => $pkg):
                            // ANIMATION LOGIC: First 2 are static (visible instantly), rest animate
                            $animClass = ($index >= 2) ? 'package-card-animate' : '';
                            ?>
                            <div class="package-card h-full <?php echo $animClass; ?>">
                                <a href="<?php echo package_url($pkg['slug']); ?>"
                                    class="block rounded-3xl overflow-hidden group shadow-creative hover:shadow-creative-hover transition-all duration-300 ease-out flex flex-col h-full bg-white border border-slate-100 will-change-transform relative z-0">

                                    <!-- Image -->
                                    <div class="relative h-64 overflow-hidden shrink-0 z-0">
                                        <img src="<?php echo base_url($pkg['image']); ?>"
                                            alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                                            loading="<?php echo $index < 2 ? 'eager' : 'lazy'; ?>"
                                            class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105 will-change-transform"
                                            onerror="this.src='https://placehold.co/600x400?text=Image+Not+Found'">

                                        <!-- Gradient -->
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60">
                                        </div>

                                        <!-- Badges: High Z-Index -->
                                        <?php if ($pkg['isPopular']): ?>
                                            <div
                                                class="absolute top-4 left-4 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1 z-20">
                                                🔥 POPULAR
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($pkg['is_new'])): ?>
                                            <div
                                                class="absolute top-4 <?php echo $pkg['isPopular'] ? 'left-28' : 'left-4'; ?> bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse z-20">
                                                NEW
                                            </div>
                                        <?php endif; ?>

                                        <!-- Price Badge -->
                                        <?php
                                        $travelersCount = isset($_GET['travelers']) ? max(1, intval($_GET['travelers'])) : 1;
                                        $displayPrice = $pkg['price'] * $travelersCount;
                                        ?>
                                        <div
                                            class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg text-slate-800 shadow-lg z-20 border border-slate-100">
                                            <span
                                                class="text-[10px] text-slate-500 block text-right font-bold uppercase tracking-wide">Starts
                                                from</span>
                                            <span
                                                class="text-lg font-black text-primary font-heading">₹<?php echo number_format($displayPrice); ?></span>
                                            <?php if ($travelersCount > 1): ?>
                                                <span class="text-[10px] text-slate-400 block text-right">for
                                                    <?php echo $travelersCount; ?> ppl</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6 flex flex-col flex-1 relative z-10 bg-white">
                                        <h3
                                            class="text-xl font-heading font-black text-slate-900 group-hover:text-primary transition-colors mb-2 leading-tight">
                                            <?php echo htmlspecialchars($pkg['title']); ?>
                                        </h3>

                                        <div class="flex items-center gap-4 text-sm font-medium text-slate-600 mb-4">
                                            <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md">
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($pkg['duration']); ?>
                                            </div>
                                            <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md">
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($pkg['location']); ?>
                                            </div>
                                        </div>

                                        <!-- Themes & Activities Tags -->
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <?php
                                            // Helper to parse JSON or Array
                                            $parseTags = function ($data) {
                                                return is_string($data) ? json_decode($data, true) : $data;
                                            };

                                            $themes = !empty($pkg['themes']) ? $parseTags($pkg['themes']) : [];
                                            $activities = !empty($pkg['activities']) ? $parseTags($pkg['activities']) : [];

                                            // Merge and take top 3 distinct
                                            $allTags = array_merge(
                                                is_array($themes) ? $themes : [],
                                                is_array($activities) ? $activities : []
                                            );
                                            $displayTags = array_slice(array_unique(array_filter($allTags)), 0, 3);

                                            foreach ($displayTags as $tag):
                                                ?>
                                                <span
                                                    class="inline-block text-[10px] uppercase font-bold tracking-wider px-2 py-1 rounded bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                    <?php echo htmlspecialchars($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($allTags) > 3): ?>
                                                <span
                                                    class="inline-block text-[10px] font-bold text-slate-400 px-1 py-1">+<?php echo count($allTags) - 3; ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Features (First 2) -->
                                        <div class="space-y-1.5 mb-6 flex-1">
                                            <?php
                                            $feats = !empty($pkg['features']) ? array_slice($pkg['features'], 0, 2) : [];
                                            if (!empty($feats)):
                                                foreach ($feats as $f): ?>
                                                    <div class="flex items-center text-sm text-slate-700">
                                                        <svg class="w-4 h-4 text-emerald-500 mr-2 shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span class="truncate font-medium"><?php echo htmlspecialchars($f); ?></span>
                                                    </div>
                                                <?php endforeach;
                                            else: ?>
                                                <p class="text-sm text-slate-400 italic">View details for more info.</p>
                                            <?php endif; ?>
                                        </div>

                                        <div
                                            class="w-full bg-slate-50 text-slate-800 font-bold py-3 rounded-xl border border-slate-200 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-colors text-center shadow-sm">
                                            View Details
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-20 text-center">
                            <div class="inline-block p-6 rounded-full bg-slate-50 mb-4">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">No packages found</h3>
                            <p class="text-slate-500">Try adjusting your filters or search terms.</p>
                            <a href="<?php echo base_url('pages/packages.php'); ?>"
                                class="inline-block mt-4 text-primary font-bold hover:underline">Clear Filters</a>
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

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        gsap.registerPlugin(ScrollTrigger);

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

        // Robust Batch Animation for Grid
        const initBatchAnimation = (selector) => {
            const elements = gsap.utils.toArray(selector);
            if (elements.length === 0) return;

            // Set initial state
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
                // Optional: Hide efficiently when leaving (optional)
                // onLeave: batch => gsap.set(batch, {opacity: 0, y: -50, overwrite: true}), 
            });
        };

        // Smooth Card Batch Animations (Trigger on .package-card-animate only)
        initBatchAnimation('.package-card-animate');
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>