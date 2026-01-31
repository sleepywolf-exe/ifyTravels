<?php
// mobile/explore.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Explore";
include __DIR__ . '/../includes/mobile_header.php';

try {
    $db = Database::getInstance();

    // Build Query
    $query = "SELECT * FROM packages WHERE 1=1";
    $params = [];
    $activeCategory = $_GET['category'] ?? 'All';
    $searchTerm = $_GET['search'] ?? '';

    if (!empty($searchTerm)) {
        $search = '%' . $searchTerm . '%';
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
    }

    if ($activeCategory !== 'All') {
        $category = '%' . $activeCategory . '%';
        $query .= " AND (description LIKE ? OR title LIKE ?)"; // Quick hack since no categories table yet
        $params[] = $category;
        $params[] = $category;
    }

    $query .= " ORDER BY id DESC";

    $packages = $db->fetchAll($query, $params);

    // Only fetch top destinations if on "All" to avoid clutter
    $destinations = ($activeCategory === 'All' && empty($searchTerm))
        ? $db->fetchAll("SELECT * FROM destinations")
        : [];

} catch (Exception $e) {
    $packages = [];
    $destinations = [];
}
?>

<div class="mt-4">
    <div class="px-4 mb-4">
        <h1 class="text-3xl font-heading font-black text-slate-900 mb-1">Explore</h1>
        <p class="text-slate-500">Find your next getaway.</p>
    </div>
    <div class="pb-24">
        <!-- Category Pills (Sticky) -->
        <div class="lazy-sticky top-16 z-40 bg-slate-50/95 backdrop-blur-sm py-2 pl-4 border-b border-slate-100 mb-6">
            <div class="swiper tags-swiper !overflow-visible">
                <div class="swiper-wrapper">
                    <?php
                    $tags = ['All', 'Beaches', 'Mountains', 'Honeymoon', 'City', 'Camping', 'Luxury'];
                    foreach ($tags as $tag):
                        $isActive = ($activeCategory === $tag);
                        $url = base_url('mobile/explore.php?category=' . urlencode($tag));
                        ?>
                        <div class="swiper-slide !w-auto">
                            <a href="<?php echo $url; ?>"
                                class="block px-5 py-2.5 rounded-full text-sm font-semibold transition-all <?php echo $isActive ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-slate-600 border border-slate-200 active:scale-95'; ?>">
                                <?php echo $tag; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($searchTerm)): ?>
            <div class="px-4 mb-4 flex items-center gap-2">
                <span class="text-slate-400">Results for:</span>
                <span class="font-bold text-slate-900">"<?php echo htmlspecialchars($searchTerm); ?>"</span>
                <a href="<?php echo base_url('mobile/explore.php'); ?>"
                    class="ml-auto text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full">Clear</a>
            </div>
        <?php endif; ?>

        <!-- Destinations Grid (Only on All) -->
        <?php if (!empty($destinations)): ?>
            <div class="px-4 mb-8">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Destinations</h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($destinations as $dest): ?>
                        <a href="<?php echo destination_url($dest['slug']); ?>"
                            class="relative h-28 rounded-2xl overflow-hidden shadow-md group">
                            <img src="<?php echo base_url($dest['image_url']); ?>"
                                class="w-full h-full object-cover group-active:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span class="text-white font-bold text-lg"><?php echo $dest['name']; ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Packages List -->
        <div class="px-4 space-y-6 pb-safe">
            <h2 class="text-xl font-bold text-slate-900 mb-4">
                <?php echo empty($packages) ? 'No Results Found' : 'Available Packages'; ?>
            </h2>

            <?php foreach ($packages as $pkg): ?>
                <a href="<?php echo package_url($pkg['slug']); ?>"
                    class="block bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-100 group">
                    <div class="relative h-48">
                        <img src="<?php echo base_url($pkg['image_url']); ?>"
                            class="w-full h-full object-cover transition-transform duration-700 group-active:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div
                            class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                            <?php echo $pkg['duration']; ?>
                        </div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <h3 class="font-bold text-lg leading-tight mb-0.5"><?php echo $pkg['title']; ?></h3>
                        </div>
                    </div>
                    <div class="p-4 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Starting from</p>
                            <span
                                class="text-primary font-black text-xl">₹<?php echo number_format($pkg['price']); ?></span>
                        </div>
                        <span
                            class="px-4 py-2 bg-slate-900 text-white text-sm font-bold rounded-full shadow-lg shadow-slate-900/20 group-active:scale-95 transition-transform">View</span>
                    </div>
                </a>
            <?php endforeach; ?>

            <?php if (empty($packages)): ?>
                <div class="text-center py-10">
                    <div class="inline-block p-4 rounded-full bg-slate-100 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-500">We couldn't find any packages matching your criteria.</p>
                    <a href="<?php echo base_url('mobile/explore.php'); ?>"
                        class="inline-block mt-4 text-primary font-bold">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        var tagsSwiper = new Swiper('.tags-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 10,
            freeMode: true,
        });
    </script>

    <?php include __DIR__ . '/../includes/mobile_footer.php'; ?>