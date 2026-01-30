<?php
// Destination Details Page
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php';

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
    <div id="error-state" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4 pt-32 bg-charcoal">
        <h1 class="text-6xl font-bold text-gray-700 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-white mb-2">Destination Not Found</h2>
        <a href="' . base_url('pages/destinations.php') . '" class="glass-button mt-4">Browse Destinations</a>
    </div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// FIX: Ensure we have the ID for fetching packages
$id = $dest['id'];

$pageTitle = $dest['name'];
include __DIR__ . '/../includes/header.php';
$packages = getPackagesByDestination($id);
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "TouristDestination",
  "name": "<?php echo htmlspecialchars($dest['name'], ENT_QUOTES); ?>",
  "description": "<?php echo htmlspecialchars(strip_tags($dest['description']), ENT_QUOTES); ?>",
  "image": "<?php echo base_url($dest['image']); ?>",
  "touristType": "<?php echo htmlspecialchars($dest['type'], ENT_QUOTES); ?>",
  "geo": {
    "@type": "GeoCoordinates",
    "addressCountry": "<?php echo htmlspecialchars($dest['country'], ENT_QUOTES); ?>"
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
    "name": "Destinations",
    "item": "<?php echo base_url('pages/destinations.php'); ?>"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "<?php echo htmlspecialchars($dest['name'], ENT_QUOTES); ?>",
    "item": "<?php echo $metaUrl; ?>"
  }]
}
</script>

<div id="content-area" class="flex-1 bg-charcoal min-h-screen">
    <!-- Hero Section (Immersive) -->
    <div class="relative h-[85vh]">
        <div class="absolute inset-0">
            <img src="<?php echo base_url($dest['image']); ?>" 
                 alt="<?php echo htmlspecialchars($dest['name']); ?>"
                 class="w-full h-full object-cover brightness-[0.6] parallax-img">
            <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-transparent to-black/30"></div>
        </div>

        <div class="absolute inset-0 flex items-end">
            <div class="container mx-auto px-6 pb-20">
                <div class="animate-fade-in-up max-w-4xl">
                    <span class="inline-block py-1 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-gold text-xs font-bold tracking-[0.2em] uppercase mb-6">
                        <?php echo $dest['type']; ?>
                    </span>
                    <h1 class="text-6xl md:text-8xl font-heading font-bold text-white mb-6 leading-none">
                        <?php echo htmlspecialchars($dest['name']); ?>
                    </h1>
                    
                    <div class="flex items-center gap-6 text-gray-300 text-lg font-light">
                        <div class="flex items-center gap-2">
                            <span class="text-gold">★</span>
                            <span class="font-medium text-white"><?php echo $dest['rating']; ?></span>
                        </div>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                        <span><?php echo $dest['country']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-16 -mt-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-12">

                <!-- About Section -->
                <div class="glass-form !p-10 !bg-white/5 border border-white/10 rounded-3xl">
                    <h2 class="text-3xl font-heading font-bold mb-6 text-white flex items-center gap-3">
                        <span class="w-12 h-1 bg-secondary rounded-full"></span>
                        About <?php echo htmlspecialchars($dest['name']); ?>
                    </h2>
                    <p class="text-gray-300 leading-relaxed text-lg font-light">
                        <?php echo $dest['description']; ?>
                    </p>

                    <!-- Highlights -->
                    <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div class="text-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:border-secondary/30 transition group">
                            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 text-gold group-hover:bg-secondary group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <span class="font-bold text-gray-300 block text-sm">Luxury Stays</span>
                        </div>
                        <div class="text-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:border-secondary/30 transition group">
                            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 text-teal-400 group-hover:bg-primary group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <span class="font-bold text-gray-300 block text-sm">Sightseeing</span>
                        </div>
                        <div class="text-center p-6 bg-white/5 rounded-2xl border border-white/5 hover:border-secondary/30 transition group">
                            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-400 group-hover:bg-purple-500 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span class="font-bold text-gray-300 block text-sm">Culture</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Map -->
                <?php if (!empty($dest['map_embed'])): ?>
                    <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl overflow-hidden">
                        <h2 class="text-3xl font-heading font-bold mb-6 text-white flex items-center gap-3">
                            <span class="w-10 h-1 bg-secondary rounded-full"></span>
                            Location
                        </h2>
                        <div class="rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                            <style> .map-container iframe { width: 100%; height: 400px; border: 0; filter: grayscale(100%) invert(92%) contrast(83%); } </style>
                            <div class="map-container relative z-0">
                                <?php echo $dest['map_embed']; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Available Packages -->
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-3xl font-heading font-bold text-white">Available Packages</h3>
                        <span class="px-4 py-1.5 rounded-full bg-white/10 border border-white/10 text-xs text-gray-300 uppercase tracking-widest">
                            <?php echo count($packages); ?> Offers
                        </span>
                    </div>

                    <?php if (count($packages) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php foreach ($packages as $index => $p): ?>
                                <a href="<?php echo package_url($p['slug']); ?>" 
                                   class="package-card block glass-card-dark rounded-3xl overflow-hidden group hover:border-secondary/50 transition-all duration-500 opacity-0 transform translate-y-8"
                                   style="transition-delay: <?php echo $index * 100; ?>ms">
                                    <div class="relative h-60 overflow-hidden">
                                        <img src="<?php echo base_url($p['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($p['title']); ?>"
                                             loading="lazy"
                                             class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                        <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-transparent to-transparent opacity-80"></div>
                                        <div class="absolute top-4 right-4 bg-white/10 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-white border border-white/10">
                                            <?php echo $p['duration']; ?>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h4 class="font-heading font-bold text-xl text-white mb-2 group-hover:text-secondary transition leading-tight">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </h4>
                                        
                                        <div class="flex flex-wrap gap-2 mb-6">
                                            <?php if (!empty($p['activities'])): ?>
                                                <?php foreach(array_slice($p['activities'], 0, 2) as $act): ?>
                                                    <span class="text-[10px] text-gray-400 border border-white/10 px-2 py-1 rounded-md bg-white/5"><?php echo htmlspecialchars($act); ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex items-end justify-between border-t border-white/10 pt-4">
                                            <div>
                                                <span class="text-xs text-gray-500 font-medium block uppercase tracking-wider">Starting From</span>
                                                <span class="text-2xl font-bold text-white">₹<?php echo number_format($p['price']); ?></span>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center group-hover:bg-secondary transition-colors">
                                                &rarr;
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="glass-form text-center p-12 border border-dashed border-white/20 rounded-3xl">
                            <div class="w-20 h-20 bg-white/5 rounded-full mx-auto flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Coming Soon</h3>
                            <p class="text-gray-400 max-w-md mx-auto mb-8">We are currently curating exclusive experiences for this destination.</p>
                            <a href="<?php echo base_url('pages/contact.php'); ?>" class="glass-button">Request Custom Quote</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Column: Sidebar -->
            <aside class="w-full">
                <div class="sticky top-32 space-y-8">
                    <!-- Quick Facts -->
                    <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl">
                        <h3 class="font-heading font-bold text-xl mb-6 text-white flex items-center gap-2">
                            <span class="text-secondary">✦</span> Quick Facts
                        </h3>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between items-center py-3 border-b border-white/5">
                                <span class="text-gray-400">Type</span>
                                <span class="font-bold text-white"><?php echo $dest['type']; ?></span>
                            </li>
                            <li class="flex justify-between items-center py-3 border-b border-white/5">
                                <span class="text-gray-400">Best Time</span>
                                <span class="font-bold text-white">All Year</span>
                            </li>
                            <li class="flex justify-between items-center py-3 border-b border-white/5">
                                <span class="text-gray-400">Currency</span>
                                <span class="font-bold text-white">Local / USD</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl">
                        <h3 class="font-heading font-bold text-xl mb-6 text-white">Why Choose Us?</h3>
                        <div class="space-y-4">
                            <?php 
                            $trustItems = [
                                'Secure Payment' => 'text-green-400', 
                                '24/7 Support' => 'text-blue-400', 
                                'Verified Quality' => 'text-secondary', 
                                'Best Price' => 'text-purple-400'
                            ];
                            foreach($trustItems as $item => $color): ?>
                                <div class="flex items-center text-sm text-gray-300">
                                    <svg class="w-5 h-5 mr-3 <?php echo $color; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <?php echo $item; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="bg-gradient-to-br from-secondary/20 to-secondary/5 rounded-3xl p-8 text-center border border-secondary/20">
                        <h4 class="font-bold text-white text-xl mb-2">Need Help?</h4>
                        <p class="text-gray-400 text-sm mb-6">Our experts are ready to craft your dream trip.</p>
                        <a href="<?php echo base_url('pages/contact.php'); ?>" class="block w-full bg-white text-charcoal font-bold py-3 rounded-xl hover:bg-gray-100 transition">Contact Concierge</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        gsap.registerPlugin(ScrollTrigger);
        
        // Parallax Hero Image
        gsap.to(".parallax-img", {
            yPercent: 20,
            ease: "none",
            scrollTrigger: {
                trigger: "body",
                start: "top top",
                end: "bottom top",
                scrub: true
            }
        });

        // Staggered Packages
        gsap.utils.toArray('.package-card').forEach(card => {
            gsap.to(card, {
                scrollTrigger: { trigger: card, start: "top 90%" },
                y: 0, opacity: 1, duration: 0.8, ease: "power2.out"
            });
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>