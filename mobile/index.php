<?php
// mobile/index.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Home";
include __DIR__ . '/../includes/mobile_header.php';

// Fetch Data for Mobile View
try {
    $db = Database::getInstance();
    $destinations = $db->fetchAll("SELECT * FROM destinations LIMIT 8");
    $popularPackages = $db->fetchAll("SELECT * FROM packages WHERE is_popular = 1 LIMIT 5");
    $stories = [
        ['name' => 'Maldives', 'img' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=200&h=200&fit=crop'],
        ['name' => 'Bali', 'img' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=200&h=200&fit=crop'],
        ['name' => 'Dubai', 'img' => 'https://images.unsplash.com/photo-1512453979798-5ea90b798d19?w=200&h=200&fit=crop'],
        ['name' => 'Paris', 'img' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=200&h=200&fit=crop'],
        ['name' => 'Swiss', 'img' => 'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?w=200&h=200&fit=crop'],
    ];
} catch (Exception $e) {
    // Fallback
    $destinations = [];
    $popularPackages = [];
}
?>

<!-- STORIES (Instagram Style) -->
<section class="mt-4 pl-4 overflow-hidden">
    <div class="swiper stories-swiper !overflow-visible">
        <div class="swiper-wrapper">
            <!-- Add Trip Story -->
            <div class="swiper-slide !w-16 flex flex-col items-center gap-1 group">
                <div
                    class="w-16 h-16 rounded-full bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center relative p-0.5">
                    <div class="w-full h-full bg-white rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span
                        class="absolute bottom-0 right-0 w-4 h-4 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border border-white">+</span>
                </div>
                <span class="text-[10px] font-medium text-slate-500">Plan Trip</span>
            </div>

            <?php foreach ($stories as $story): ?>
                <div class="swiper-slide !w-16 flex flex-col items-center gap-1 cursor-pointer">
                    <div class="w-16 h-16 rounded-full p-[2px] bg-gradient-to-tr from-yellow-400 to-primary">
                        <div class="w-full h-full rounded-full border-2 border-white overflow-hidden">
                            <img src="<?php echo $story['img']; ?>" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <span class="text-[10px] font-medium text-slate-700 truncate w-full text-center">
                        <?php echo $story['name']; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CATEGORIES (Pills) -->
<section class="mt-8 pl-4 overflow-hidden">
    <!-- Section Header -->
    <div class="flex justify-between items-end pr-4 mb-4">
        <h2 class="text-xl font-heading font-bold text-slate-900">Categories</h2>
    </div>

    <div class="swiper tags-swiper !overflow-visible">
        <div class="swiper-wrapper">
            <?php
            $tags = ['All', 'Beaches', 'Mountains', 'Honeymoon', 'City', 'Camping', 'Luxury'];
            foreach ($tags as $i => $tag):
                ?>
                <div class="swiper-slide !w-auto">
                    <button
                        class="px-5 py-2.5 rounded-full text-sm font-semibold transition-all <?php echo $i === 0 ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-slate-600 border border-slate-200'; ?>">
                        <?php echo $tag; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- POPULAR PACKAGES (Card Swiper) -->
<section class="mt-10 px-0 overflow-hidden">
    <div class="flex justify-between items-end px-4 mb-4">
        <h2 class="text-xl font-heading font-bold text-slate-900">Popular <span class="text-primary">Packages</span>
        </h2>
        <a href="<?php echo base_url('mobile/explore.php'); ?>" class="text-primary text-sm font-semibold">See All</a>
    </div>

    <div class="swiper cards-swiper pl-4 !overflow-visible pb-10">
        <div class="swiper-wrapper">
            <?php foreach ($popularPackages as $pkg): ?>
                <div class="swiper-slide !w-[280px]">
                    <a href="<?php echo package_url($pkg['slug']); ?>"
                        class="block relative h-[350px] rounded-[2rem] overflow-hidden shadow-xl bg-slate-900 group">
                        <img src="<?php echo base_url($pkg['image_url']); ?>"
                            class="w-full h-full object-cover opacity-80 group-active:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>

                        <div
                            class="absolute top-4 right-4 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold text-white uppercase border border-white/20">
                            <?php echo $pkg['duration']; ?>
                        </div>

                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-xl font-bold text-white mb-1 leading-tight">
                                <?php echo $pkg['title']; ?>
                            </h3>
                            <p class="text-slate-300 text-xs mb-3 line-clamp-1">
                                <?php echo strip_tags($pkg['description']); ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-white">₹
                                    <?php echo number_format($pkg['price']); ?>
                                </span>
                                <span
                                    class="w-8 h-8 rounded-full bg-white text-slate-900 flex items-center justify-center">→</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DESTINATIONS (Grid) -->
<section class="px-4 mt-2 mb-10">
    <div class="flex justify-between items-end mb-4">
        <h2 class="text-xl font-heading font-bold text-slate-900">Top Destinations</h2>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <?php foreach ($destinations as $dest): ?>
            <a href="<?php echo destination_url($dest['slug']); ?>"
                class="relative aspect-square rounded-[1.5rem] overflow-hidden shadow-md active:scale-95 transition-transform">
                <img src="<?php echo base_url($dest['image_url']); ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-4">
                    <span class="text-white font-bold text-sm">
                        <?php echo $dest['name']; ?>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<script>
    var storiesSwiper = new Swiper('.stories-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 12,
        freeMode: true,
    });

    var tagsSwiper = new Swiper('.tags-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 10,
        freeMode: true,
    });

    var cardsSwiper = new Swiper('.cards-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
    });
</script>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>