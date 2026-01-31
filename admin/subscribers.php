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
                        <a href="?export=csv" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 flex items-center gap-2">
                            <i class="fas fa-file-csv"></i> <span>Export CSV</span>
                        </a>
                        <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2">
                            <i class="fas fa-print"></i> <span>Print</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Subscribers</p>
                            <h3 class="text-2xl font-bold text-slate-900"><?php echo number_format($totalRecords); ?></h3>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
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
                         <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Growth Rate</p>
                            <h3 class="text-2xl font-bold text-slate-900">+12% <span class="text-xs text-green-500 font-normal">vs last month</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
                    <form class="relative w-full md:w-96">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by email..." 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all">
                    </form>
                    
                     <div class="text-sm text-slate-500">
                        Showing <?php echo count($subscribers); ?> of <?php echo $totalRecords; ?> records
                    </div>
                </div>

                <!-- List Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-200">
                                    <th class="p-5 font-bold text-slate-600 text-xs uppercase tracking-wider">ID</th>
                                    <th class="p-5 font-bold text-slate-600 text-xs uppercase tracking-wider">Subscriber Details</th>
                                    <th class="p-5 font-bold text-slate-600 text-xs uppercase tracking-wider">Joined Date</th>
                                    <th class="p-5 font-bold text-slate-600 text-xs uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($subscribers)): ?>
                                        <tr>
                                            <td colspan="4" class="p-16 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-inbox text-slate-300 text-3xl"></i>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-900">No subscribers found</h3>
                                                    <p class="text-slate-500 max-w-xs mx-auto mt-1">
                                                        <?php echo $search ? "Try adjusting your search terms." : "Share your newsletter form to get started!"; ?>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                <?php else: ?>
                                        <?php foreach ($subscribers as $sub): ?>
                                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                                    <td class="p-5 text-slate-400 font-mono text-sm leading-none">
                                                        #<?php echo str_pad($sub['id'], 5, '0', STR_PAD_LEFT); ?>
                                                    </td>
                                                    <td class="p-5">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center text-sm font-bold uppercase">
                                                                <?php echo substr($sub['email'], 0, 1); ?>
                                                            </div>
                                                            <div>
                                                                <a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block">
                                                                    <?php echo htmlspecialchars($sub['email']); ?>
                                                                </a>
                                                                <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">Active</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="p-5 text-slate-500 text-sm">
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-slate-700"><?php echo date('M j, Y', strtotime($sub['created_at'])); ?></span>
                                                            <span class="text-xs text-slate-400"><?php echo date('h:i A', strtotime($sub['created_at'])); ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-5 text-right">
                                                        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this subscriber? This cannot be undone.');" class="inline-block relative top-1">
                                                            <input type="hidden" name="delete_id" value="<?php echo $sub['id']; ?>">
                                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete">
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
                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Previous</a>
                                <?php endif; ?>
                            
                                <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Next</a>
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