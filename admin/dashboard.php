<?php
// admin/dashboard.php
require 'auth_check.php';

$db = Database::getInstance();

// Fetch stats efficiently
$stats = [
    'destinations' => $db->fetch("SELECT COUNT(*) as count FROM destinations")['count'] ?? 0,
    'packages' => $db->fetch("SELECT COUNT(*) as count FROM packages")['count'] ?? 0,
    'bookings' => $db->fetch("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0,
    'inquiries' => $db->fetch("SELECT COUNT(*) as count FROM inquiries WHERE status = 'New'")['count'] ?? 0,
    'revenue' => $db->fetch("SELECT SUM(p.price) as total FROM bookings b LEFT JOIN packages p ON b.package_id = p.id WHERE b.status != 'Cancelled'")['total'] ?? 0
];

// Recent bookings
$recentBookings = $db->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen">

    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome,
                    <strong><?php echo e($_SESSION['admin_name'] ?? 'Admin'); ?></strong></span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Bookings</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['bookings']); ?></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-blue-600 text-2xl">📅</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Revenue</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                ₹<?php echo number_format($stats['revenue']); ?></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg text-green-600 text-2xl">💰</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Active Packages</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['packages']); ?></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg text-purple-600 text-2xl">📦</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">New Inquiries</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['inquiries']); ?></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg text-orange-600 text-2xl">📩</div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">Recent Bookings</h2>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Package</th>
                            <th class="px-6 py-3">Travel Date</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($recentBookings) > 0): ?>
                            <?php foreach ($recentBookings as $b): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-gray-800 font-medium"><?php echo e($b['customer_name']); ?></td>
                                    <td class="px-6 py-3 text-gray-600"><?php echo e($b['package_name']); ?></td>
                                    <td class="px-6 py-3 text-gray-600"><?php echo e($b['travel_date']); ?></td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        <?php echo $b['status'] == 'Confirmed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'; ?>">
                                            <?php echo e($b['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No bookings yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>