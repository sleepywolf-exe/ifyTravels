<?php
$pageTitle = "About Us - Our Journey";
include __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[60vh] min-h-[500px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80&w=2000"
            class="w-full h-full object-cover object-center brightness-[0.40]" alt="About Us">
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/20 to-black/60"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center pt-24">
        <span
            class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-bold tracking-widest uppercase text-sm rounded-full mb-6 animate-fade-in-up">
            Our Story
        </span>
        <h1
            class="text-4xl md:text-5xl lg:text-7xl font-heading font-bold text-white mb-6 drop-shadow-2xl animate-fade-in-up delay-100">
            Curating <span class="text-primary">Dreams</span>
        </h1>
        <p
            class="text-white/90 text-base md:text-xl font-light leading-relaxed max-w-2xl mx-auto drop-shadow-lg animate-fade-in-up delay-200">
            We don't just plan trips; we craft unforgettable experiences that stay with you forever.
        </p>
    </div>
</section>

<!-- Our Mission & Vision -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <!-- Content -->
            <div class="relative z-10">
                <h2 class="text-3xl lg:text-5xl font-heading font-bold text-slate-900 mb-6 lg:mb-8 leading-tight">
                    Redefining <span class="text-primary italic">Luxury</span> Travel
                </h2>
                <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                    At <span class="font-bold text-slate-800">ifyTravels</span>, we believe that travel is the only
                    thing you buy that makes you richer. Founded on the principle of "Experience First," we've dedicated
                    ourselves to unearthing the world's hidden gems and crafting bespoke journeys for the modern
                    discernment traveler.
                </p>
                <p class="text-lg text-slate-600 mb-10 leading-relaxed">
                    Whether it's a private sunset dinner in Santorini, a trek through the misty Himalayas, or a luxury
                    safari in Kenya, our team of expert curators ensures every detail is perfect.
                </p>

                <div class="grid grid-cols-2 gap-8">
                    <div class="border-l-4 border-primary pl-6">
                        <h4 class="text-4xl font-bold text-slate-900 mb-2">10k+</h4>
                        <p class="text-slate-500 font-medium uppercase tracking-wider text-sm">Happy Travelers</p>
                    </div>
                    <div class="border-l-4 border-secondary pl-6">
                        <h4 class="text-4xl font-bold text-slate-900 mb-2">50+</h4>
                        <p class="text-slate-500 font-medium uppercase tracking-wider text-sm">Destinations</p>
                    </div>
                </div>
            </div>

            <!-- Image Composition -->
            <div class="relative">
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl -z-10"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-secondary/5 rounded-full blur-3xl -z-10"></div>

                <div
                    class="relative rounded-[2.5rem] overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                    <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&q=80&w=1000"
                        alt="Travel Experience" class="w-full h-auto object-cover">
                    <!-- Overlay Badge -->
                    <div
                        class="absolute bottom-8 right-8 bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-lg max-w-xs transform hover:scale-105 transition-transform">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Client Love</div>
                        </div>
                        <p class="text-slate-800 italic font-medium text-sm">"The attention to detail was impeccable.
                            Best trip of my life!"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section (Glassmorphism) -->
<section class="py-24 bg-slate-50 relative">
    <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-3xl md:text-4xl font-heading font-bold text-slate-900 mb-6">Why Travel With Us?</h2>
            <p class="text-slate-500 text-lg">We don't just book tickets; we design memories. Here is what makes us
                different.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div
                class="bg-white p-10 rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 group">
                <div
                    class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Expert Curation</h3>
                <p class="text-slate-500 leading-relaxed">Our travel experts have personally visited every destination
                    we offer, ensuring you get only the best.</p>
            </div>

            <!-- Card 2 -->
            <div
                class="bg-white p-10 rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 group">
                <div
                    class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Personalized Care</h3>
                <p class="text-slate-500 leading-relaxed">From the moment you inquire until you return home, we are with
                    you 24/7. Your comfort is our priority.</p>
            </div>

            <!-- Card 3 -->
            <div
                class="bg-white p-10 rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-slate-100 group">
                <div
                    class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Best Price Guarantee</h3>
                <p class="text-slate-500 leading-relaxed">Luxury doesn't have to break the bank. We negotiate the best
                    rates so you can enjoy more for less.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-slate-900">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=2000"
            class="w-full h-full object-cover opacity-20" alt="Beach">
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center">
        <h2 class="text-3xl md:text-6xl font-heading font-bold text-white mb-6 md:mb-8 max-w-4xl mx-auto leading-tight">
            Ready to Start Your Journey?
        </h2>
        <p class="text-xl text-white/70 mb-10 max-w-2xl mx-auto">
            Let's plan your next adventure together. Reach out to our team today.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo base_url('contact'); ?>"
                class="px-10 py-4 bg-primary text-white text-lg font-bold rounded-full shadow-lg shadow-primary/30 hover:bg-secondary hover:scale-105 transition-all duration-300">
                Contact Us
            </a>
            <a href="<?php echo base_url('destinations'); ?>"
                class="px-10 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white text-lg font-bold rounded-full hover:bg-white hover:text-slate-900 transition-all duration-300">
                Explore Destinations
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>