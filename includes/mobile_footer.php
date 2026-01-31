<?php
// includes/mobile_footer.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- BOTTOM NAVIGATION BAR (Fixed) -->
<nav class="fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 z-[999] pb-[env(safe-area-inset-bottom)]">
    <div class="flex justify-around items-center h-16">

        <!-- Home -->
        <a href="<?php echo base_url('mobile/index.php'); ?>"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 <?php echo ($current_page == 'index.php') ? 'text-primary' : 'text-slate-400'; ?>">
            <svg class="w-6 h-6 <?php echo ($current_page == 'index.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="text-[10px] font-medium tracking-wide">Home</span>
        </a>

        <!-- Explore -->
        <a href="<?php echo base_url('mobile/explore.php'); ?>"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 <?php echo ($current_page == 'explore.php') ? 'text-primary' : 'text-slate-400'; ?>">
            <svg class="w-6 h-6 <?php echo ($current_page == 'explore.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <span class="text-[10px] font-medium tracking-wide">Explore</span>
        </a>

        <!-- Saved -->
        <a href="<?php echo base_url('mobile/saved.php'); ?>"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 <?php echo ($current_page == 'saved.php') ? 'text-primary' : 'text-slate-400'; ?>">
            <svg class="w-6 h-6 <?php echo ($current_page == 'saved.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span class="text-[10px] font-medium tracking-wide">Saved</span>
        </a>

        <!-- Account -->
        <a href="<?php echo base_url('mobile/profile.php'); ?>"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 <?php echo ($current_page == 'profile.php') ? 'text-primary' : 'text-slate-400'; ?>">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="w-6 h-6 rounded-full overflow-hidden border border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'U'); ?>&background=random"
                        class="w-full h-full object-cover">
                </div>
            <?php else: ?>
                <svg class="w-6 h-6 <?php echo ($current_page == 'profile.php') ? 'fill-current' : 'fill-none stroke-current stroke-2'; ?>"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            <?php endif; ?>
            <span class="text-[10px] font-medium tracking-wide">Account</span>
        </a>
    </div>
</nav>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Initialize standard swipers
    document.addEventListener('DOMContentLoaded', function () {
        // Example global init if needed, usually done in pages
    });
</script>
</body>

</html>