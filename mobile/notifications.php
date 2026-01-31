<?php
// mobile/notifications.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Notifications";
include __DIR__ . '/../includes/mobile_header.php';
?>

<div class="px-4 mt-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-heading font-black text-slate-900">Inbox</h1>
        <button class="text-sm font-bold text-primary">Mark all read</button>
    </div>

    <div class="space-y-4">
        <!-- New -->
        <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-100 flex gap-4 relative overflow-hidden">
            <div
                class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-primary/10 to-transparent rounded-bl-full pointer-events-none">
            </div>
            <div class="w-2 h-2 rounded-full bg-red-500 mt-2 flex-shrink-0"></div>

            <div class="flex-1">
                <h3 class="font-bold text-slate-900 text-sm mb-1">Welcome Gift! 🎁</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-2">Thanks for installing our app. Use code
                    <strong>APP10</strong> for 10% off your first booking.</p>
                <span class="text-[10px] text-slate-400 font-medium">2 mins ago</span>
            </div>
        </div>

        <!-- Old -->
        <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-100 flex gap-4 opacity-70">
            <div class="w-2 h-2 rounded-full bg-slate-200 mt-2 flex-shrink-0"></div>

            <div class="flex-1">
                <h3 class="font-bold text-slate-900 text-sm mb-1">New Destination Added</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-2">Explore the beautiful landscapes of Vietnam with
                    our new packages.</p>
                <span class="text-[10px] text-slate-400 font-medium">1 day ago</span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>