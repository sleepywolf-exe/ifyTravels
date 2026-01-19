<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../data/loader.php';

$db = Database::getInstance();
$partnerId = $_SESSION['partner_id'];

// Fetch Partner Details
$partner = $db->fetch("SELECT * FROM affiliates WHERE id = ?", [$partnerId]);
$commissionRate = $partner['commission_rate'] ?? 10.00;

// stats
try {
    // 1. Total Clicks
    $clickResult = $db->fetch("SELECT COUNT(*) as count FROM referral_clicks WHERE affiliate_id = ?", [$partnerId]);
    $totalClicks = $clickResult['count'] ?? 0;

    // 2. Total Leads (All Bookings)
    $leadsResult = $db->fetch("SELECT COUNT(*) as count FROM bookings WHERE affiliate_id = ?", [$partnerId]);
    $totalLeads = $leadsResult['count'] ?? 0;

    // 3. Conversions (Confirmed Sales)
    $conversionResult = $db->fetch("SELECT COUNT(*) as count, SUM(total_price) as total_revenue FROM bookings WHERE affiliate_id = ? AND status = 'Confirmed'", [$partnerId]);
    $totalConversions = $conversionResult['count'] ?? 0;
    $confirmedRevenue = $conversionResult['total_revenue'] ?? 0;

    // 4. Earnings (Based on Confirmed Revenue)
    $totalEarnings = ($confirmedRevenue * $commissionRate) / 100;

    // 5. Recent Clicks
    $recentClicks = $db->fetchAll("SELECT * FROM referral_clicks WHERE affiliate_id = ? ORDER BY created_at DESC LIMIT 10", [$partnerId]);

} catch (Exception $e) {
    // handle error
    $totalClicks = 0;
    $totalLeads = 0;
    $totalConversions = 0;
    $totalEarnings = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Partner Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glassmorphism.css'); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#D97706',
                    },
                    fontFamily: { heading: ['Poppins', 'sans-serif'], body: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-white min-h-screen relative overflow-x-hidden">

    <!-- Background -->
    <div
        class="fixed inset-0 bg-[url('../assets/images/travel-doodles.png')] opacity-5 bg-repeat bg-center pointer-events-none">
    </div>
    <div class="fixed inset-0 bg-gradient-to-tr from-gray-900/95 via-gray-900/90 to-primary/10 pointer-events-none">
    </div>

    <!-- Layout -->
    <div class="relative z-10 flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 glass-form !rounded-none !border-l-0 !border-y-0 fixed h-full hidden md:flex flex-col">
            <div class="mb-10 px-4">
                <img src="<?php echo base_url('assets/images/logo-white.png'); ?>" alt="Logo" class="h-10">
                <p class="text-xs text-gray-500 mt-2 ml-1">PARTNER PORTAL</p>
            </div>

            <nav class="flex-1 space-y-2 px-2">
                <a href="dashboard.php"
                    class="flex items-center space-x-3 px-4 py-3 bg-white/10 rounded-xl text-white font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="profile.php"
                    class="flex items-center space-x-3 px-4 py-3 hover:bg-white/5 rounded-xl text-gray-300 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>My Profile</span>
                </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center mb-4 gap-3">
                    <div
                        class="w-12 h-12 shrink-0 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center font-bold text-white text-lg shadow-lg border-2 border-white/10">
                        <?php echo strtoupper(substr($partner['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="font-medium text-sm">
                            <?php echo htmlspecialchars($partner['name']); ?>
                        </p>
                        <p class="text-xs text-gray-500 truncate w-32">
                            <?php echo htmlspecialchars($partner['email']); ?>
                        </p>
                    </div>
                </div>
                <a href="logout.php" class="block text-center py-2 text-sm text-red-400 hover:text-red-300">Sign Out</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-4 md:p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold font-heading">Dashboard</h1>
                    <p class="text-gray-400 text-sm">Track your performance and earnings</p>
                </div>
                <div class="md:hidden">
                    <!-- Mobile Menu Button Placeholder -->
                    <a href="logout.php" class="text-sm text-red-400">Logout</a>
                </div>
            </header>

            <!-- Ref Link Card -->
            <div
                class="glass-card !bg-primary/20 !border-primary/30 p-6 rounded-2xl mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-lg mb-1 text-teal-300">Your Referral Link</h3>
                    <p class="text-sm text-gray-300">Share this link to earn
                        <?php echo number_format($commissionRate, 1); ?>% commission on bookings.
                    </p>
                </div>
                <div class="flex w-full md:w-auto gap-2">
                    <code
                        class="bg-black/30 px-4 py-3 rounded-lg text-teal-100 font-mono text-sm flex-1 md:flex-none truncate">
                        <?php echo base_url('?ref=' . $partner['code']); ?>
                    </code>
                    <button
                        onclick="navigator.clipboard.writeText('<?php echo base_url('?ref=' . $partner['code']); ?>'); alert('Copied!');"
                        class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-white font-medium transition">
                        Copy
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Clicks -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-400 font-medium text-sm border-b border-gray-600 pb-1">CLICKS</h3>
                        <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold font-heading text-white">
                        <?php echo number_format($totalClicks); ?>
                    </p>
                </div>

                <!-- Leads -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-400 font-medium text-sm border-b border-gray-600 pb-1">LEADS</h3>
                        <div class="p-2 bg-purple-500/20 rounded-lg text-purple-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold font-heading text-white">
                        <?php echo number_format($totalLeads); ?>
                    </p>
                </div>

                <!-- Conversions (Sales) -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-400 font-medium text-sm border-b border-gray-600 pb-1">CONVERSIONS</h3>
                        <div class="p-2 bg-green-500/20 rounded-lg text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold font-heading text-white">
                        <?php echo number_format($totalConversions); ?>
                    </p>
                </div>

                <!-- Earnings -->
                <div class="glass-card p-6 rounded-2xl relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-secondary/20 rounded-full blur-2xl"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <h3 class="text-gray-400 font-medium text-sm border-b border-gray-600 pb-1">EARNINGS</h3>
                        <div class="p-2 bg-yellow-500/20 rounded-lg text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold font-heading text-white relative z-10">₹
                        <?php echo number_format($totalEarnings); ?>
                    </p>
                </div>
            </div>

            <!-- Recent Activity -->
            <h2 class="text-xl font-bold mb-4 font-heading">Recent Clicks</h2>
            <div class="glass-card rounded-2xl overflow-hidden overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-gray-400 text-sm uppercase tracking-wider">
                            <th class="p-4 font-medium">Time</th>
                            <th class="p-4 font-medium">IP Address</th>
                            <th class="p-4 font-medium">Referrer</th>
                            <th class="p-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        <?php if (!empty($recentClicks)): ?>
                            <?php foreach ($recentClicks as $click): ?>
                                <tr class="hover:bg-white/5 transition">
                                    <td class="p-4 text-gray-300">
                                        <?php echo date('M j, Y h:i A', strtotime($click['created_at'])); ?>
                                    </td>
                                    <td class="p-4 font-mono text-xs text-gray-400">
                                        <?php echo htmlspecialchars($click['ip_address'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="p-4 text-gray-400 truncate max-w-xs"
                                        title="<?php echo htmlspecialchars($click['referrer_url'] ?? 'Direct'); ?>">
                                        <?php echo htmlspecialchars($click['referrer_url'] ?? 'Direct'); ?>
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-500/10 text-green-400">
                                            Valid
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500">No activity recorded yet. Start
                                    sharing your link!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>

</html>