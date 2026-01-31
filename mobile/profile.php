<?php
// mobile/profile.php
require_once __DIR__ . '/../includes/functions.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(base_url('pages/login.php'));
}

$pageTitle = "My Profile";
include __DIR__ . '/../includes/mobile_header.php';
?>

<div class="bg-slate-900 pb-20 pt-8 rounded-b-[2.5rem] relative overflow-hidden">
    <div class="absolute inset-0 bg-primary/20 opacity-30"></div>
    <div class="px-6 relative z-10 flex flex-col items-center">
        <div class="w-24 h-24 rounded-full border-4 border-white/20 shadow-xl overflow-hidden mb-4">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=random&size=200"
                class="w-full h-full object-cover">
        </div>
        <h1 class="text-2xl font-bold text-white mb-1">
            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Traveler'); ?>
        </h1>
        <p class="text-slate-300 text-sm">
            <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
        </p>

        <div class="mt-6 flex gap-4 w-full">
            <div class="flex-1 bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/5">
                <span class="block text-2xl font-black text-white">0</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">Trips</span>
            </div>
            <div class="flex-1 bg-white/10 backdrop-blur-md rounded-2xl p-4 text-center border border-white/5">
                <span class="block text-2xl font-black text-white">0</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">Points</span>
            </div>
        </div>
    </div>
</div>

<div class="px-4 -mt-10 relative z-20 space-y-4 pb-32">
    <!-- Menu -->
    <div class="bg-white rounded-3xl shadow-lg p-2">
        <a href="<?php echo base_url('mobile/bookings.php'); ?>"
            class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-colors">
            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">My Bookings</h3>
                <p class="text-xs text-slate-500">View past and upcoming trips</p>
            </div>
            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="#"
            class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-colors border-t border-slate-100">
            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">Settings</h3>
                <p class="text-xs text-slate-500">Account and preferences</p>
            </div>
            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <!-- Logout -->
    <a href="<?php echo base_url('services/logout.php'); ?>"
        class="flex items-center justify-center gap-2 w-full p-4 text-red-500 font-bold bg-white rounded-2xl shadow-sm border border-slate-100 mt-4 active:bg-red-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        Log Out
    </a>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>