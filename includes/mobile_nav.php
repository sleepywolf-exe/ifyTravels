<?php
// includes/mobile_nav.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- MOBILE BOTTOM NAVIGATION (Fixed) -->
<div
    class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-xl border-t border-slate-200 z-[9999] md:hidden pb-safe flex justify-around items-center h-20 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">

    <!-- Home -->
    <a href="<?php echo base_url('mobile/index.php'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'index.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'index.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Home</span>
    </a>

    <!-- Explore -->
    <a href="<?php echo base_url('mobile/explore.php'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'explore.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'explore.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Explore</span>
    </a>

    <!-- Trips (Bookings) -->
    <!-- Center Action Button -->
    <a href="<?php echo base_url('mobile/search.php'); ?>" class="relative -top-5">
        <div
            class="w-14 h-14 bg-gradient-to-br from-primary to-teal-600 rounded-full shadow-lg shadow-primary/40 flex items-center justify-center transform transition-transform active:scale-95 text-white border-4 border-slate-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </a>

    <!-- Saved -->
    <a href="<?php echo base_url('mobile/saved.php'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'saved.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
        <div class="relative p-1">
            <svg class="w-6 h-6 transition-transform duration-300 group-active:scale-90 <?php echo ($current_page == 'saved.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </div>
        <span class="text-[10px] font-bold tracking-wide">Saved</span>
    </a>

    <!-- Profile -->
    <a href="<?php echo isLoggedIn() ? base_url('mobile/profile.php') : base_url('mobile/login.php'); ?>"
        class="flex flex-col items-center justify-center w-full h-full space-y-1 group <?php echo ($current_page == 'profile.php' || $current_page == 'login.php' || $current_page == 'bookings.php') ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">
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
            <?php echo isLoggedIn() ? 'Account' : 'Profile'; ?>
        </span>
    </a>

</div>

<!-- Safe Area Spacer -->
<div class="md:hidden h-20"></div>