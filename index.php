<?php
$pageTitle = "Experience the Extraordinary";
$isHome = true;
include 'includes/header.php';
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "<?php echo get_setting('site_name', 'ifyTravels'); ?>",
  "url": "<?php echo base_url(); ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?php echo base_url('packages?search={search_term_string}'); ?>",
    "query-input": "required name=search_term_string"
  }
}
</script>

<?php
// Data Logic
$destinations = $destinations ?? [];
$featured = array_filter($destinations, fn($d) => !empty($d['is_featured']));
$others = array_filter($destinations, fn($d) => empty($d['is_featured']));
$topDestinations = array_merge($featured, $others);
$topDestinations = array_slice($topDestinations, 0, 4);

$packages = $packages ?? [];
$popularPackages = array_filter($packages, fn($p) => $p['isPopular']);
if(empty($popularPackages)) $popularPackages = array_slice($packages, 0, 3);
$popularPackages = array_slice($popularPackages, 0, 3);

$testimonials = [];
try {
    $db = Database::getInstance();
    $testimonials = $db->fetchAll("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3");
} catch (Exception $e) {}
?>

<!-- PARALLAX BACKGROUND (Fixed) -->
<div class="fixed inset-0 z-0">
    <img src="<?php echo get_setting('hero_bg', 'https://images.unsplash.com/photo-1540206351-d6465b3ac5c1?auto=format&fit=crop&q=80&w=2000'); ?>" 
         alt="Maldives Luxury" 
         class="w-full h-full object-cover parallax-bg brightness-[0.6]">
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-charcoal"></div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<main class="relative z-10">

    <!-- HERO SECTION -->
    <section class="min-h-screen flex flex-col items-center justify-center pt-32 pb-12 overflow-hidden relative">
        <div class="container mx-auto px-4 text-center z-20">
            <!-- Animated Hero Title -->
            <h1 class="hero-title opacity-0 transform translate-y-10 text-5xl sm:text-7xl md:text-9xl font-bold mb-6 font-heading tracking-tight leading-none text-white drop-shadow-2xl">
                Experience the <br />
                <span class="text-gold italic pr-2 font-serif">Extraordinary</span>
            </h1>
            
            <p class="hero-subtitle opacity-0 transform translate-y-10 text-lg md:text-2xl text-gray-200 mb-12 font-light max-w-2xl mx-auto tracking-wide">
                <?php echo get_setting('hero_subtitle', 'Curated luxury travel experiences designed just for you.'); ?>
            </p>

            <!-- Glass Booking Form -->
            <div class="hero-form opacity-0 transform translate-y-10 max-w-5xl mx-auto w-full relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-secondary to-teal-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                <div class="glass-form p-6 md:p-10 rounded-2xl relative bg-charcoal/30 border border-white/10 backdrop-blur-xl shadow-2xl">
                    <form action="<?php echo base_url('pages/packages.php'); ?>" method="GET" class="space-y-6">
                        
                        <!-- Tabs -->
                        <div class="flex justify-center md:justify-start mb-6">
                            <button type="button" class="px-8 py-2 rounded-full text-white font-bold bg-white/10 border border-white/20 backdrop-blur-md">Search Packages</button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                            <!-- Destination -->
                            <div class="md:col-span-4 text-left">
                                <label class="glass-label text-gray-300 text-sm ml-1">Destination</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gold"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    <select name="destination" class="glass-select w-full !pl-12 !bg-white/5 !border-white/10 focus:!border-secondary text-white">
                                        <option value="" class="text-charcoal">Where to?</option>
                                        <?php foreach ($destinations as $dest): ?>
                                            <option value="<?php echo $dest['id']; ?>" class="text-charcoal"><?php echo htmlspecialchars($dest['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div class="md:col-span-3 text-left">
                                <label class="glass-label text-gray-300 text-sm ml-1">Date</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gold"><path fill="currentColor" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                                    <input type="text" id="departure-date" name="date" class="glass-input w-full !pl-12 !bg-white/5 !border-white/10 focus:!border-secondary text-white placeholder-gray-400" placeholder="Select Date">
                                </div>
                            </div>

                            <!-- Travelers -->
                            <div class="md:col-span-3 text-left">
                                <label class="glass-label text-gray-300 text-sm ml-1">Travelers</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gold"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                    <input type="number" name="travelers" min="1" class="glass-input w-full !pl-12 !bg-white/5 !border-white/10 focus:!border-secondary text-white placeholder-gray-400" placeholder="Guests">
                                </div>
                            </div>

                            <!-- Button -->
                            <div class="md:col-span-2">
                                <button type="submit" class="w-full bg-gradient-to-r from-secondary to-yellow-600 hover:from-yellow-500 hover:to-secondary text-white font-bold py-3 rounded-xl shadow-lg transform hover:-translate-y-1 transition duration-300">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <div class="w-[30px] h-[50px] rounded-full border-2 border-white/30 flex justify-center p-2">
                <div class="w-1 h-3 bg-white rounded-full animate-scroll"></div>
            </div>
        </div>
    </section>

    <!-- CONTENT SECTIONS (Dark Background) -->
    <div class="bg-charcoal relative z-10 rounded-t-[3rem] -mt-12 shadow-[0_-20px_60px_rgba(0,0,0,0.5)] border-t border-white/5">
        
        <!-- DESTINATIONS -->
        <section class="py-24">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-20 section-header opacity-0 transform translate-y-10">
                    <span class="text-secondary font-bold tracking-widest uppercase text-sm">Discover</span>
                    <h2 class="text-4xl md:text-5xl font-heading font-bold text-white mt-2 mb-6">Trending <span class="text-gold">Destinations</span></h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-secondary to-transparent mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-2">
                    <?php foreach ($topDestinations as $index => $dest): ?>
                        <div class="destination-card opacity-0 transform translate-y-10" style="transition-delay: <?php echo $index * 100; ?>ms">
                            <a href="<?php echo destination_url($dest['slug']); ?>" class="block group relative h-[500px] rounded-[2rem] overflow-hidden">
                                <img src="<?php echo base_url($dest['image']); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>
                                
                                <div class="absolute bottom-0 left-0 w-full p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-3xl font-heading font-bold text-white mb-2"><?php echo htmlspecialchars($dest['name']); ?></h3>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1 text-gold">
                                            <span>★</span> <span class="text-white font-medium"><?php echo $dest['rating']; ?></span>
                                        </div>
                                        <span class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white group-hover:bg-secondary transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-16">
                    <a href="<?php echo base_url('/destinations'); ?>" class="inline-block px-10 py-4 border border-white/20 rounded-full text-white font-medium hover:bg-white hover:text-charcoal transition-all duration-300">View All Destinations</a>
                </div>
            </div>
        </section>

        <!-- PACKAGES (Featured) -->
        <?php if (!empty($popularPackages)): ?>
        <section class="py-24 bg-dark relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="container mx-auto px-6 relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 section-header opacity-0 transform translate-y-10">
                    <div>
                        <span class="text-secondary font-bold tracking-widest uppercase text-sm">Exclusive</span>
                        <h2 class="text-4xl md:text-5xl font-heading font-bold text-white mt-2">Popular <span class="text-gold">Packages</span></h2>
                    </div>
                    <a href="<?php echo base_url('/packages'); ?>" class="hidden md:flex items-center gap-2 text-gray-400 hover:text-secondary transition-colors">See all offers <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <?php foreach ($popularPackages as $index => $pkg): ?>
                        <div class="package-card opacity-0 transform translate-y-10" style="transition-delay: <?php echo $index * 150; ?>ms">
                            <a href="<?php echo package_url($pkg['slug']); ?>" class="block glass-card-dark rounded-3xl overflow-hidden group hover:border-secondary/50 transition-all duration-500">
                                <div class="relative h-72 overflow-hidden">
                                    <div class="absolute top-4 left-4 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full z-10">FEATURED</div>
                                    <img src="<?php echo base_url($pkg['image']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                                </div>
                                <div class="p-8">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-2xl font-bold text-white group-hover:text-secondary transition-colors"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                                    </div>
                                    <div class="flex items-center gap-4 text-gray-400 text-sm mb-6">
                                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <?php echo htmlspecialchars($pkg['duration']); ?></span>
                                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> Global</span>
                                    </div>
                                    
                                    <div class="flex items-end justify-between border-t border-white/10 pt-6">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-widest">Starting from</p>
                                            <p class="text-3xl font-heading font-bold text-white">₹<?php echo number_format($pkg['price']); ?></p>
                                        </div>
                                        <span class="text-secondary font-medium group-hover:translate-x-2 transition-transform">Explore &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- LUXURY CTA SECTION -->
        <section class="py-32 relative overflow-hidden">
            <div class="container mx-auto px-6 relative z-10 text-center">
                <div class="max-w-4xl mx-auto">
                    <span class="inline-block py-2 px-6 rounded-full bg-white/10 border border-white/10 text-gold text-sm font-bold tracking-[0.2em] mb-8 uppercase backdrop-blur-md">
                        Start Your Journey
                    </span>
                    <h2 class="text-5xl md:text-7xl font-heading font-bold mb-8 text-white leading-tight">
                        Ready to Explore the <br /><span class="text-gold">Extraordinary?</span>
                    </h2>
                    <p class="text-xl text-gray-400 mb-12 font-light max-w-2xl mx-auto">
                        Join the elite travelers who have discovered the world's most breathtaking destinations with ifyTravels.
                    </p>
                    <div class="flex flex-col md:flex-row gap-6 justify-center">
                        <a href="<?php echo base_url('pages/booking.php'); ?>" class="bg-gradient-to-r from-secondary to-yellow-600 text-white font-bold py-5 px-12 rounded-full shadow-lg hover:shadow-orange-500/20 transform hover:-translate-y-1 transition-all duration-300 text-lg">
                            Book Your Trip
                        </a>
                        <a href="<?php echo base_url('pages/contact.php'); ?>" class="bg-transparent border border-white/20 text-white font-bold py-5 px-12 rounded-full hover:bg-white hover:text-charcoal transition-all duration-300 text-lg">
                            Contact Concierge
                        </a>
                    </div>
                </div>
            </div>
            <!-- Glow Effects -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary/20 rounded-full blur-[120px] pointer-events-none"></div>
        </section>

    </div>
</main>

<!-- GSAP Animation Logic -->
<script>
    // Register Plugins
    gsap.registerPlugin(ScrollTrigger);

    // 1. Hero Text Reveal (Load Animation)
    const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
    
    tl.to(".hero-title", { y: 0, opacity: 1, duration: 1.5, delay: 0.2 })
      .to(".hero-subtitle", { y: 0, opacity: 1, duration: 1.2 }, "-=1")
      .to(".hero-form", { y: 0, opacity: 1, duration: 1.2 }, "-=0.8");

    // 2. Parallax Background
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

    // 3. Section Headers Reveal
    gsap.utils.toArray('.section-header').forEach(header => {
        gsap.to(header, {
            scrollTrigger: {
                trigger: header,
                start: "top 80%",
                toggleActions: "play none none reverse"
            },
            y: 0,
            opacity: 1,
            duration: 1
        });
    });

    // 4. Staggered Cards (Destinations)
    gsap.utils.toArray('.destination-card').forEach(card => {
        gsap.to(card, {
            scrollTrigger: {
                trigger: card,
                start: "top 85%"
            },
            y: 0,
            opacity: 1,
            duration: 1,
            ease: "power2.out"
        });
    });

    // 5. Staggered Cards (Packages)
    gsap.utils.toArray('.package-card').forEach(card => {
        gsap.to(card, {
            scrollTrigger: {
                trigger: card,
                start: "top 85%"
            },
            y: 0,
            opacity: 1,
            duration: 1,
            ease: "power2.out"
        });
    });

</script>

<!-- Flatpickr Script (Deferred) -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#departure-date", {
            minDate: "today",
            dateFormat: "d-M-Y",
            altInput: true,
            altFormat: "d M, Y",
            disableMobile: "true"
        });
    });
</script>

<?php include 'includes/footer.php'; ?>