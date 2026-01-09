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

$pageTitle = $dest['name'];
include __DIR__ . '/../includes/header.php';
$packages = getPackagesByDestination($id);
?>

<div id="content-area" class="flex-1">
    <!-- Hero -->
    <div class="relative h-[60vh] bg-gray-200">
        <img src="<?php echo base_url($dest['image']); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal/80 to-transparent flex items-end">
            <div class="container mx-auto px-6 pb-12 text-white">
                <span
                    class="bg-accent text-white px-3 py-1 rounded text-xs font-bold uppercase tracking-wider mb-2 inline-block">
                    <?php echo $dest['type']; ?>
                </span>
                <h1 class="text-5xl font-bold mb-2">
                    <?php echo htmlspecialchars($dest['name']); ?>
                </h1>
                <div class="flex items-center gap-2 text-white/90">
                    <span class="text-yellow-400">★</span> <span class="font-bold">
                        <?php echo $dest['rating']; ?>
                    </span> Rating
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12 flex flex-col lg:flex-row gap-12">
        <div class="lg:w-2/3">
            <h2 class="text-2xl font-bold mb-4 text-charcoal">About Destination</h2>
            <p class="text-gray-600 mb-8 leading-relaxed text-lg">
                <?php echo nl2br(htmlspecialchars($dest['description'])); ?>
            </p>

            <h3 class="text-xl font-bold mb-6 text-charcoal">Available Packages here</h3>
            <div id="related-packages" class="space-y-4">
                <?php if (count($packages) > 0): ?>
                    <?php foreach ($packages as $p): ?>
                        <div
                            class="group border border-gray-100 rounded-xl p-4 flex flex-col md:flex-row gap-6 items-center hover:shadow-lg transition bg-white">
                            <img src="<?php echo base_url($p['image']); ?>" class="rounded-lg w-full md:w-32 h-24 object-cover">
                            <div class="flex-1">
                                <h4 class="font-bold text-lg text-charcoal group-hover:text-primary transition">
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </h4>
                                <p class="text-sm text-gray-500 mb-2">
                                    <?php echo $p['duration']; ?>
                                </p>
                                <div class="flex gap-2">
                                    <?php foreach (array_slice($p['features'], 0, 2) as $f): ?>
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">
                                            <?php echo htmlspecialchars($f); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="text-center md:text-right w-full md:w-auto">
                                <div class="text-xl font-bold text-primary mb-2">₹
                                    <?php echo $p['price']; ?>
                                </div>
                                <a href="<?php echo package_url($p['slug']); ?>"
                                    class="inline-block w-full md:w-auto bg-primary text-white px-5 py-2 rounded-lg font-bold hover:bg-teal-700 transition">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 italic">No specific packages listed for this destination yet. Contact us for a
                        custom trip!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-1/3 space-y-8">
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100">
                <h3 class="font-bold text-lg mb-6 text-charcoal">Quick Facts</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <span class="text-gray-500 font-medium">Type</span>
                        <span class="font-bold text-primary">
                            <?php echo $dest['type']; ?>
                        </span>
                    </li>
                    <li class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <span class="text-gray-500 font-medium">Best Time</span>
                        <span class="font-bold text-charcoal">All Year Round</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>