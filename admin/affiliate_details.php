<?php
require 'auth_check.php';
$db = Database::getInstance();

$id = $_GET['id'] ?? null;
if (!$id) redirect('affiliates.php');

// Handle Delete
if (isset($_POST['delete_affiliate'])) {
    // Set affiliate_id to NULL for associated bookings to preserve history
    $db->execute("UPDATE bookings SET affiliate_id = NULL WHERE affiliate_id = ?", [$id]);
    $db->execute("DELETE FROM affiliates WHERE id = ?", [$id]);
    redirect('affiliates.php?msg=deleted');
}

// Handle Status Toggle
if (isset($_GET['toggle_status'])) {
    $current = $_GET['current'];
    $newStatus = ($current === 'active') ? 'inactive' : 'active';
    $db->execute("UPDATE affiliates SET status = ? WHERE id = ?", [$newStatus, $id]);
    redirect("affiliate_details.php?id=$id");
}

// Fetch Affiliate Info
$affiliate = $db->fetch("SELECT * FROM affiliates WHERE id = ?", [$id]);
if (!$affiliate) die("Affiliate not found.");

// Fetch Stats
$stats = $db->fetch("
    SELECT 
        COUNT(id) as total_bookings,
        COALESCE(SUM(total_price), 0) as total_revenue
    FROM bookings 
    WHERE affiliate_id = ?
", [$id]);

// Fetch Booking History
$bookings = $db->fetchAll("
    SELECT b.*, p.title as package_title 
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    WHERE b.affiliate_id = ?
    ORDER BY b.created_at DESC
", [$id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($affiliate['name']); ?> - Affiliate Details</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex h-screen">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="affiliates.php" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                            <?php echo e($affiliate['name']); ?>
                            <span class="text-sm px-3 py-1 rounded-full font-medium <?php echo $affiliate['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <?php echo ucfirst($affiliate['status']); ?>
                            </span>
                        </h1>
                        <p class="text-gray-500 mt-1">Code: <span class="font-mono font-bold bg-gray-100 px-2 rounded"><?php echo e($affiliate['code']); ?></span> &bull; Joined <?php echo date('M j, Y', strtotime($affiliate['created_at'])); ?></p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="?toggle_status=1&id=<?php echo $id; ?>&current=<?php echo $affiliate['status']; ?>" 
                       class="px-4 py-2 rounded-lg border font-medium transition <?php echo $affiliate['status'] === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50'; ?>">
                        <?php echo $affiliate['status'] === 'active' ? 'Deactivate Account' : 'Activate Account'; ?>
                    </a>
                    <form method="POST" onsubmit="return confirm('Are you sure? This will delete the affiliate account but keep their bookings.');">
                        <input type="hidden" name="delete_affiliate" value="1">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-gray-500 text-sm font-medium mb-1">Total Bookings</div>
                    <div class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['total_bookings']); ?></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-gray-500 text-sm font-medium mb-1">Total Revenue Generated</div>
                    <div class="text-3xl font-bold text-emerald-600">₹<?php echo number_format($stats['total_revenue']); ?></div>
                </div>
                <!-- Placeholder for Earnings (e.g. 10% commission) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-gray-500 text-sm font-medium mb-1">Estimated Earnings (10%)</div>
                    <div class="text-3xl font-bold text-blue-600">₹<?php echo number_format($stats['total_revenue'] * 0.10); ?></div>
                    <p class="text-xs text-gray-400 mt-1">* Calculation example only</p>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-800">Referral History</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                                <th class="p-4">Booking ID</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Package</th>
                                <th class="p-4">Amount</th>
                                <th class="p-4 text-center">Date</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <?php if (empty($bookings)): ?>
                                <tr><td colspan="7" class="p-8 text-center text-gray-400">No bookings found for this affiliate yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $b): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-mono text-gray-500">#<?php echo $b['id']; ?></td>
                                        <td class="p-4 font-medium text-gray-800">
                                            <?php echo e($b['customer_name']); ?><br>
                                            <span class="text-xs text-gray-500 font-normal"><?php echo e($b['email']); ?></span>
                                        </td>
                                        <td class="p-4 text-gray-600 truncate max-w-xs"><?php echo e($b['package_title']); ?></td>
                                        <td class="p-4 font-bold text-gray-800">₹<?php echo number_format($b['total_price']); ?></td>
                                        <td class="p-4 text-center text-gray-500 text-xs"><?php echo date('M j, Y', strtotime($b['created_at'])); ?></td>
                                        <td class="p-4 text-center">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                                <?php 
                                                switch($b['status']) {
                                                    case 'Confirmed': echo 'bg-green-100 text-green-700'; break;
                                                    case 'Pending': echo 'bg-yellow-100 text-yellow-700'; break;
                                                    case 'Cancelled': echo 'bg-red-100 text-red-700'; break;
                                                    default: echo 'bg-gray-100 text-gray-700';
                                                }
                                                ?>">
                                                <?php echo $b['status']; ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <a href="booking-details.php?id=<?php echo $b['id']; ?>" class="text-blue-600 hover:underline text-xs font-medium">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
