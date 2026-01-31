<?php
// mobile/search.php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = "Search";
include __DIR__ . '/../includes/mobile_header.php';
?>

<div class="px-4 mt-2">
    <!-- Search Input -->
    <form action="<?php echo base_url('mobile/explore.php'); ?>" method="GET" class="relative group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-slate-400 group-focus-within:text-primary transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input type="text" name="search" placeholder="Search destinations, packages..."
            class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-medium text-lg shadow-sm"
            autofocus>
    </form>

    <!-- Recent Searches -->
    <div class="mt-8">
        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Recent</h2>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo base_url('mobile/explore.php?search=Maldives'); ?>"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200">Maldives</a>
            <a href="<?php echo base_url('mobile/explore.php?search=Dubai'); ?>"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200">Dubai</a>
            <a href="<?php echo base_url('mobile/explore.php?search=Visa%20Free'); ?>"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200">Visa
                Free</a>
        </div>
    </div>

    <!-- Suggested -->
    <div class="mt-10">
        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Trending Now</h2>
        <div class="space-y-4">
            <a href="<?php echo base_url('mobile/explore.php?category=International'); ?>"
                class="flex items-center gap-4 py-2 border-b border-slate-100 pb-2">
                <div class="w-12 h-12 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-900">International Trips</h3>
                    <p class="text-xs text-slate-500">Popular destinations abroad</p>
                </div>
            </a>
            <a href="<?php echo base_url('mobile/explore.php?category=Deals'); ?>"
                class="flex items-center gap-4 py-2 border-b border-slate-100 pb-2">
                <div class="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-900">Last Minute Deals</h3>
                    <p class="text-xs text-slate-500">Discounts ending soon</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>