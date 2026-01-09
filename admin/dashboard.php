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

// Data for Charts
// 1. Monthly Bookings (Last 6 Months)
$months = [];
$bookingCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthLabel = date('M', strtotime("-$i months"));
    $months[] = $monthLabel;
    $count = $db->fetch("SELECT COUNT(*) as count FROM bookings WHERE DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])['count'] ?? 0;
    $bookingCounts[] = $count;
}

// 2. Inquiry Sources
$sourcesRaw = $db->fetchAll("SELECT utm_source, COUNT(*) as count FROM inquiries GROUP BY utm_source");
$sourceLabels = [];
$sourceCounts = [];
foreach ($sourcesRaw as $s) {
    // If source is empty, label it 'Direct'
    $label = empty($s['utm_source']) ? 'Direct/Organic' : ucfirst($s['utm_source']);
    $sourceLabels[] = $label;
    $sourceCounts[] = $s['count'];
}

// Recent bookings
$recentBookings = $db->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen text-gray-800">

    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 z-10">
            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-800 to-gray-600">
                Dashboard Overview</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-500">Welcome back,</span>
                <span
                    class="text-gray-800 font-bold bg-gray-100 px-3 py-1 rounded-full text-sm"><?php echo e($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 lg:p-10">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Destinations -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Destinations</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['destinations']); ?></p>
                        </div>
                        <div class="bg-teal-50 p-3 rounded-xl text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Packages -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Packages</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['packages']); ?></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Bookings</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['bookings']); ?></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Revenue</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                ₹<?php echo number_format($stats['revenue']); ?></p>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- New Inquiries -->
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">New Inquiries</p>
                            <p class="text-3xl font-bold text-gray-800 mt-2">
                                <?php echo number_format($stats['inquiries']); ?></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-xl text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Line Chart: Bookings -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Booking Trends (Last 6 Months)</h3>
                    <div class="h-64">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
                <!-- Doughnut Chart: Sources -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Lead Sources</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="sourcesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">Recent Bookings</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Package</th>
                                <th class="px-6 py-4">Travel Date</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (count($recentBookings) > 0): ?>
                                <?php foreach ($recentBookings as $b): ?>
                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="px-6 py-4 text-gray-800 font-medium"><?php echo e($b['customer_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo e($b['package_name']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo e($b['travel_date']); ?></td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                                            <?php echo $b['status'] == 'Confirmed' ? 'bg-green-100 text-green-700' :
                                                ($b['status'] == 'Cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                                                <?php echo e($b['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">No bookings yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Data passed from PHP
        const months = <?php echo json_encode($months); ?>;
        const bookingCounts = <?php echo json_encode($bookingCounts); ?>;
        const sourceLabels = <?php echo json_encode($sourceLabels); ?>;
        const sourceCounts = <?php echo json_encode($sourceCounts); ?>;

        // 1. Line Chart
        const ctxBookings = document.getElementById('bookingsChart').getContext('2d');
        new Chart(ctxBookings, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Bookings',
                    data: bookingCounts,
                    borderColor: '#2563EB', // Blue 600
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563EB',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [2, 2] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Doughnut Chart
        const ctxSources = document.getElementById('sourcesChart').getContext('2d');
        new Chart(ctxSources, {
            type: 'doughnut',
            data: {
                labels: sourceLabels,
                datasets: [{
                    data: sourceCounts,
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, usePointStyle: true }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>

</html>