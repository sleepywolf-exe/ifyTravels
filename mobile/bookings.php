<?php
// mobile/bookings.php
require_once __DIR__ . '/../includes/functions.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(base_url('pages/login.php'));
}

$pageTitle = "My Bookings";
include __DIR__ . '/../includes/mobile_header.php';

try {
    $db = Database::getInstance();
    $bookings = $db->fetchAll(
        "SELECT b.*, p.title as package_title, p.image_url 
         FROM bookings b 
         JOIN packages p ON b.package_id = p.id 
         WHERE b.user_id = ? 
         ORDER BY b.created_at DESC",
        [$_SESSION['user_id']]
    );
} catch (Exception $e) {
    $bookings = [];
}
?>

<div class="px-4 mt-6">
    <h1 class="text-3xl font-heading font-black text-slate-900 mb-6">My Bookings</h1>

    <?php if (empty($bookings)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">No bookings yet</h2>
            <p class="text-slate-500 text-sm mb-6">Your adventure awaits!</p>
            <a href="<?php echo base_url('mobile/explore.php'); ?>"
                class="px-6 py-2.5 bg-primary text-white font-bold rounded-lg shadow-lg shadow-primary/30">Find a Trip</a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookings as $booking): ?>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex gap-4">
                    <div class="w-20 h-20 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                        <img src="<?php echo base_url($booking['image_url']); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-slate-900 line-clamp-1">
                                <?php echo htmlspecialchars($booking['package_title']); ?>
                            </h3>
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700'
                            ];
                            $color = $statusColors[strtolower($booking['status'])] ?? 'bg-slate-100 text-slate-600';
                            ?>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase <?php echo $color; ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Travel Date:
                            <?php echo date('M d, Y', strtotime($booking['travel_date'])); ?>
                        </p>
                        <p class="text-sm font-black text-slate-900 mt-2">₹
                            <?php echo number_format($booking['total_price']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/mobile_footer.php'; ?>