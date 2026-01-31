<?php
// includes/mobile_nav.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- MOBILE BOTTOM NAVIGATION (Fixed) -->
<div
    class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-xl border-t border-slate-200 z-[9999] md:hidden pb-safe flex justify-around items-center h-20 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">

    <!-- Home -->
    <a href="<?php echo base_url(); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'index.php' || $current_page == '') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'index.php' || $current_page == '') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Home</span>
    </a>

    <!-- Explore -->
    <a href="<?php echo base_url('packages'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'packages.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'packages.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Explore</span>
    </a>

    <!-- Trips (Bookings) -->
    <!-- Center Action Button -->
    <a href="<?php echo base_url('contact'); ?>" class="relative -top-5">
        <div
            class="w-14 h-14 bg-gradient-to-br from-primary to-teal-600 rounded-full shadow-lg shadow-primary/40 flex items-center justify-center transform transition-transform active:scale-95 text-white border-4 border-slate-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    </a>

    <!-- Wishlist (Destinations) -->
    <a href="<?php echo base_url('destinations'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'destinations.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'destinations.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Saved</span>
    </a>

    <!-- Profile -->
    <a href="<?php echo isLoggedIn() ? base_url('user/dashboard.php') : base_url('login'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'dashboard.php' || $current_page == 'login.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <?php if (isLoggedIn()): ?>
                <div class="w-6 h-6 rounded-full bg-slate-200 overflow-hidden border border-slate-300">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=random"
                        class="w-full h-full object-cover">
                </div>
            <?php else: ?>
                <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'login.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            <?php endif; ?>
        </div>
        <span class="text-[10px] font-bold tracking-wide">
            <?php echo isLoggedIn() ? 'Account' : 'Login'; ?>
        </span>
    </a>

</div>

<!-- Safe Area Spacer -->
<div class="md:hidden h-20"></div>