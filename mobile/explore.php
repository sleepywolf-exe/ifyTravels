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

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
    }

    if (isset($_GET['category']) && !empty($_GET['category']) && $_GET['category'] !== 'All') {
        $category = '%' . $_GET['category'] . '%';
        $query .= " AND (description LIKE ? OR title LIKE ?)"; // Quick hack since no categories table yet
        $params[] = $category;
        $params[] = $category;
    }

    $query .= " ORDER BY id DESC";

    $packages = $db->fetchAll($query, $params);
    $destinations = $db->fetchAll("SELECT * FROM destinations");
} catch (Exception $e) {
    $packages = [];
    $destinations = [];
}
?>

<div class="px-4 mt-4">
    <h1 class="text-3xl font-heading font-black text-slate-900 mb-2">Explore</h1>
    <p class="text-slate-500 mb-6">Find your next getaway.</p>

    <!-- Search Input -->
    <a href="<?php echo base_url('mobile/search.php'); ?>"
        class="block w-full bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 text-slate-400 mb-8 shadow-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span>Where to next?</span>
    </a>

    <!-- Destinations Grid -->
    <h2 class="text-xl font-bold text-slate-900 mb-4">Destinations</h2>
    <div class="grid grid-cols-2 gap-4 mb-8">
        <?php foreach ($destinations as $dest): ?>
            <a href="<?php echo destination_url($dest['slug']); ?>"
                class="relative h-28 rounded-2xl overflow-hidden shadow-md group">
                <img src="<?php echo base_url($dest['image_url']); ?>"
                    class="w-full h-full object-cover group-active:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span class="text-white font-bold text-lg">
                        <?php echo $dest['name']; ?>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- All Packages -->
    <h2 class="text-xl font-bold text-slate-900 mb-4">All Packages</h2>
    <div class="space-y-6">
        <?php foreach ($packages as $pkg): ?>
            <a href="<?php echo package_url($pkg['slug']); ?>"
                class="block bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-100">
                <div class="relative h-48">
                    <img src="<?php echo base_url($pkg['image_url']); ?>" class="w-full h-full object-cover">
                    <div
                        class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        <?php echo $pkg['duration']; ?>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-xl font-bold text-slate-900 mb-1">
                        <?php echo $pkg['title']; ?>
                    </h3>
                    <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold mb-4">By IfyTravels</p>
                    <div class="flex justify-between items-center">
                        <span class="text-primary font-black text-xl">₹
                            <?php echo number_format($pkg['price']); ?>
                        </span>
                        <span class="px-4 py-2 bg-slate-900 text-white text-sm font-bold rounded-full">View</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>