<?php
// mobile/saved.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Saved Trips";
include __DIR__ . '/../includes/mobile_header.php';
?>

<div class="px-6 py-6 pb-24 space-y-6">
    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
            </path>
        </svg>
    </div>

    <h1 class="text-2xl font-heading font-bold">Saved Trips</h1>
    <p class="text-slate-500 mb-8 max-w-xs mx-auto">Start exploring and save your favorite packages to access them here.
    </p>

    <a href="<?php echo base_url('mobile/explore.php'); ?>"
        class="px-8 py-3 bg-primary text-white font-bold rounded-full shadow-lg shadow-primary/30 active:scale-95 transition-transform">
        Explore Packages
    </a>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>