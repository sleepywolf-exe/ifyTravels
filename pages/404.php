<?php
http_response_code(404);
$pageTitle = "Lost in Paradise";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-charcoal flex items-center justify-center relative overflow-hidden">

    <!-- Background Video/Image -->
    <div class="absolute inset-0">
        <img src="<?php echo base_url('assets/images/destinations/dubai.jpg'); ?>"
            class="w-full h-full object-cover opacity-20 blur-sm" alt="404 Background">
        <div class="absolute inset-0 bg-gradient-to-b from-charcoal via-charcoal/90 to-charcoal"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center animate-fade-in-up">

        <h1 class="text-[8rem] md:text-[16rem] font-heading font-bold text-white/5 leading-none select-none">404</h1>

        <div class="-mt-20 md:-mt-32 relative">
            <span class="text-secondary font-bold tracking-[0.3em] uppercase text-sm mb-4 block">Destination
                Unknown</span>
            <h2 class="text-4xl md:text-5xl font-heading font-bold text-white mb-6">It Seems You're Lost</h2>
            <p class="text-gray-400 text-lg max-w-lg mx-auto mb-10 font-light">
                The page you are looking for has either drifted away or doesn't exist. Let's get you back on course.
            </p>

            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="<?php echo base_url(); ?>" class="glass-button px-8">
                    Return Home
                </a>
                <a href="<?php echo base_url('pages/contact.php'); ?>"
                    class="glass-button bg-white/5 border-white/20 hover:bg-white/10 px-8">
                    Contact Concierge
                </a>
            </div>
        </div>

        <div class="mt-20 border-t border-white/5 pt-10 max-w-4xl mx-auto">
            <p class="text-gray-500 text-sm mb-6">Or explore our trending destinations</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo base_url('destinations/kashmir'); ?>"
                    class="px-5 py-2 rounded-full border border-white/10 text-gray-400 hover:text-white hover:border-secondary hover:bg-secondary/10 transition text-sm">Kashmir</a>
                <a href="<?php echo base_url('destinations/dubai'); ?>"
                    class="px-5 py-2 rounded-full border border-white/10 text-gray-400 hover:text-white hover:border-secondary hover:bg-secondary/10 transition text-sm">Dubai</a>
                <a href="<?php echo base_url('destinations/maldives'); ?>"
                    class="px-5 py-2 rounded-full border border-white/10 text-gray-400 hover:text-white hover:border-secondary hover:bg-secondary/10 transition text-sm">Maldives</a>
                <a href="<?php echo base_url('pages/packages.php'); ?>"
                    class="px-5 py-2 rounded-full border border-white/10 text-gray-400 hover:text-white hover:border-secondary hover:bg-secondary/10 transition text-sm">All
                    Packages</a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>