<?php
$pageTitle = "Newsletter Subscribers";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';

$db = Database::getInstance();

// Handle Delete
if (isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $db->execute("DELETE FROM newsletter_subscribers WHERE id = ?", [$deleteId]);
    header("Location: subscribers.php?msg=deleted");
    exit;
}

// Handle Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $subscribers = $db->fetchAll("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC");

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email', 'Joined Date']);

    foreach ($subscribers as $sub) {
        fputcsv($output, [
            $sub['id'],
            $sub['email'],
            $sub['created_at']
        ]);
    }
    fclose($output);
    exit;
}

// Search & Pagination Logic
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$whereClause = "";
$params = [];

if ($search) {
    $whereClause = "WHERE email LIKE ?";
    $params[] = "%$search%";
}

// Count Total
$totalStmt = $db->fetch("SELECT COUNT(*) as c FROM newsletter_subscribers $whereClause", $params);
$totalRecords = $totalStmt['c'];
$totalPages = ceil($totalRecords / $limit);

// Fetch Data
$query = "SELECT * FROM newsletter_subscribers $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$subscribers = $db->fetchAll($query, $params);


// Include Header/Sidebar
// Note: Adapting to admin layout structure
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?> - Admin
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar_inc.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 transition-all duration-300">
            <div class="max-w-7xl mx-auto">
                <!-- Header & Actions -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-heading font-bold text-slate-900">Newsletter Subscribers</h1>
                        <p class="text-slate-500 mt-1">Manage and export your email list.</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="?export=csv"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 flex items-center gap-2">
                            <i class="fas fa-file-csv"></i> <span>Export CSV</span>
                        </a>
                        <button onclick="window.print()"
                            class="bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2">
                            <i class="fas fa-print"></i> <span>Print</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Subscribers</p>
                            <h3 class="text-2xl font-bold text-slate-900"><?php echo number_format($totalRecords); ?>
                            </h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">This Month</p>
                            <h3 class="text-2xl font-bold text-slate-900">
                                <?php
                                $thisMonth = $db->fetch("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
                                echo number_format($thisMonth['c'] ?? 0);
                                ?>
                            </h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Growth Rate</p>
                            <h3 class="text-2xl font-bold text-slate-900">+12% <span
                                    class="text-xs text-green-500 font-normal">vs last month</span></h3>
                        </div>
                    </div>
                </div>

                <!-- List Card (Search + Table) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                    <!-- Toolbar -->
                    <div
                        class="p-5 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-50/30">
                        <form class="relative w-full md:w-96 group">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Search subscribers..."
                                class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-700">
                        </form>

                        <div class="text-sm font-medium text-slate-500">
                            Showing <span class="text-slate-900 font-bold"><?php echo count($subscribers); ?></span> of
                            <span class="text-slate-900 font-bold"><?php echo $totalRecords; ?></span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse table-fixed">
                            <thead>
                                <tr class="bg-white border-b border-slate-100">
                                    <th
                                        class="p-5 pl-8 font-extrabold text-slate-400 text-[11px] uppercase tracking-widest w-[10%]">
                                        ID</th>
                                    <th
                                        class="p-5 font-extrabold text-slate-400 text-[11px] uppercase tracking-widest w-[55%]">
                                        Subscriber</th>
                                    <th
                                        class="p-5 font-extrabold text-slate-400 text-[11px] uppercase tracking-widest w-[20%]">
                                        Joined</th>
                                    <th
                                        class="p-5 pr-8 font-extrabold text-slate-400 text-[11px] uppercase tracking-widest text-right w-[15%]">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 relative">
                                <?php if (empty($subscribers)): ?>
                                    <tr>
                                        <td colspan="4" class="p-20 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div
                                                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-slate-50/50">
                                                    <i class="fas fa-envelope-open-text text-slate-300 text-4xl"></i>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-900 mb-2">No subscribers yet</h3>
                                                <p class="text-slate-500 max-w-sm mx-auto leading-relaxed">
                                                    <?php echo $search ? "We couldn't find any subscribers matching your search." : "Your list is currently empty. Share your newsletter form to get started!"; ?>
                                                </p>
                                                <?php if ($search): ?>
                                                    <a href="subscribers.php"
                                                        class="mt-6 text-indigo-600 font-bold hover:text-indigo-700 hover:underline">Clear
                                                        Search</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subscribers as $sub): ?>
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            <td class="p-5 pl-8 text-slate-400 font-mono text-sm leading-none font-medium">
                                                #<?php echo str_pad($sub['id'], 5, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td class="p-5">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 text-indigo-600 flex items-center justify-center text-sm font-black border border-indigo-100/50 shadow-sm">
                                                        <?php echo strtoupper(substr($sub['email'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>"
                                                            class="font-bold text-slate-800 hover:text-indigo-600 transition-colors block text-[15px]">
                                                            <?php echo htmlspecialchars($sub['email']); ?>
                                                        </a>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            <span class="text-xs text-slate-400 font-medium">Subscribed</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-5">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-bold text-slate-700 text-sm"><?php echo date('M d, Y', strtotime($sub['created_at'])); ?></span>
                                                    <span
                                                        class="text-xs text-slate-400 font-medium mt-0.5"><?php echo date('h:i A', strtotime($sub['created_at'])); ?></span>
                                                </div>
                                            </td>
                                            <td class="p-5 pr-8 text-right">
                                                <form method="POST"
                                                    onsubmit="return confirm('Permanently delete this subscriber?');"
                                                    class="inline-block">
                                                    <input type="hidden" name="delete_id" value="<?php echo $sub['id']; ?>">
                                                    <button type="submit"
                                                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all focus:ring-2 focus:ring-red-500/20"
                                                        title="Remove Subscriber">
                                                        <i class="fas fa-trash-alt text-sm"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-between items-center">
                            <div class="text-sm text-slate-500">
                                Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                            </div>
                            <div class="flex gap-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"
                                        class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Previous</a>
                                <?php endif; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"
                                        class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Next</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>

</html>