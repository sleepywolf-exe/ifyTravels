<?php
require 'auth_check.php';
$db = Database::getInstance();

$message = '';
$error = '';

// Handle Bulk Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    $ids = $_POST['booking_ids'] ?? [];
    if (!empty($ids)) {
        // Sanitize integers
        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $deleteSql = "DELETE FROM bookings WHERE id IN ($placeholders)";
        if ($db->execute($deleteSql, $ids)) {
            $message = count($ids) . " booking(s) deleted successfully.";
        } else {
            $error = "Failed to delete selected bookings.";
        }
    }
}

// 1. Handle Filters
$statusFilter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$sourceFilter = isset($_GET['source']) ? sanitize_input($_GET['source']) : '';

// 2. Handle Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

// 3. Build Query
$sql = "SELECT * FROM bookings WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if (!empty($sourceFilter)) {
    // Basic source filtering
    $sql .= " AND utm_source LIKE ?";
    $params[] = "%$sourceFilter%";
}

// 4. Get Total Count for Pagination
$countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$totalRows = $db->fetch($countSql, $params)['total'] ?? 0;
$totalPages = ceil($totalRows / $limit);

// 5. Fetch Data with Limit
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$bookings = $db->fetchAll($sql, $params);

// Get unique sources for filter dropdown
$sources = $db->fetchAll("SELECT DISTINCT utm_source FROM bookings WHERE utm_source IS NOT NULL AND utm_source != ''");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bookings - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
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

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Bookings</h1>
                <p class="text-gray-500 mt-1">Manage all your package bookings</p>
                <?php if ($message): ?>
                    <p
                        class="text-green-600 text-sm mt-2 font-medium bg-green-50 px-3 py-1 rounded-lg inline-block border border-green-100">
                        <i class="fas fa-check-circle mr-1"></i> <?php echo $message; ?>
                    </p>
                <?php endif; ?>
                <?php if ($error): ?>
                    <p
                        class="text-red-600 text-sm mt-2 font-medium bg-red-50 px-3 py-1 rounded-lg inline-block border border-red-100">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?php echo $error; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Filters & Search -->
            <div class="flex items-center gap-3">
                <!-- Delete Button (Hidden by default) -->
                <button type="button" id="delete-btn" onclick="confirmDelete()"
                    class="hidden bg-red-50 text-red-600 hover:bg-red-100 px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 border border-red-200">
                    <i class="fas fa-trash-alt"></i> Delete Selected
                </button>

                <form class="flex flex-wrap gap-3 bg-white p-2 rounded-xl border border-gray-100 shadow-sm"
                    method="GET">
                    <select name="status"
                        class="bg-gray-50 border-0 rounded-lg text-sm text-gray-600 font-medium focus:ring-2 focus:ring-blue-100 py-2.5 pl-3 pr-8 w-36">
                        <option value="">All Status</option>
                        <option value="Confirmed" <?php echo $statusFilter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed
                        </option>
                        <option value="Pending" <?php echo $statusFilter == 'Pending' ? 'selected' : ''; ?>>Pending
                        </option>
                        <option value="Cancelled" <?php echo $statusFilter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled
                        </option>
                    </select>

                    <select name="source"
                        class="bg-gray-50 border-0 rounded-lg text-sm text-gray-600 font-medium focus:ring-2 focus:ring-blue-100 py-2.5 pl-3 pr-8 w-36">
                        <option value="">All Sources</option>
                        <?php foreach ($sources as $s): ?>
                            <option value="<?php echo e($s['utm_source']); ?>" <?php echo $sourceFilter == $s['utm_source'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst($s['utm_source']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Filter
                    </button>

                    <?php if ($statusFilter || $sourceFilter): ?>
                        <a href="bookings.php"
                            class="text-gray-400 hover:text-red-500 px-3 py-2.5 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </form>
        </header>

        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
            <form method="POST" id="bulk-form">
                <input type="hidden" name="action" value="bulk_delete">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead
                            class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase font-bold text-gray-400 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-200 h-4 w-4 cursor-pointer">
                                </th>
                                <th class="px-6 py-4">Ref ID</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Package</th>
                                <th class="px-6 py-4">Travel Date</th>
                                <th class="px-6 py-4 text-center">Source</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (count($bookings) > 0): ?>
                                <?php foreach ($bookings as $b): ?>
                                    <tr class="hover:bg-blue-50/40 transition group">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="booking_ids[]" value="<?php echo $b['id']; ?>"
                                                class="booking-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-200 h-4 w-4 cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs font-semibold text-blue-600">
                                            #<?php echo $b['id']; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900"><?php echo e($b['customer_name']); ?></div>
                                            <div class="text-xs text-gray-400 mt-0.5"><?php echo e($b['email']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-700">
                                            <?php echo e($b['package_name'] ?? 'Custom Package'); ?>
                                            <?php if ($b['total_price'] == 0): ?>
                                                <span
                                                    class="inline-block ml-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold uppercase tracking-wide rounded">Custom
                                                    Request</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500"><?php echo e($b['travel_date']); ?></td>

                                        <!-- Source Icon -->
                                        <td class="px-6 py-4 text-center">
                                            <?php if (!empty($b['utm_source'])): ?>
                                                <div class="flex justify-center items-center"
                                                    title="Source: <?php echo ucfirst($b['utm_source']); ?>">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center shadow-sm">
                                                        <?php echo get_source_icon_svg($b['utm_source']); ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-300">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold capitalize
                                            <?php echo $b['status'] == 'Confirmed' ? 'bg-green-100 text-green-700 border border-green-200' :
                                                ($b['status'] == 'Cancelled' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-yellow-50 text-yellow-700 border border-yellow-100'); ?>">
                                                <?php echo e($b['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="booking-details.php?id=<?php echo $b['id']; ?>"
                                                class="text-blue-600 hover:text-blue-800 font-semibold text-xs uppercase tracking-wide hover:underline">View
                                                Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <span class="text-4xl mb-3">📭</span>
                                            <p>No bookings found matching your filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span
                            class="font-medium"><?php echo min($offset + $limit, $totalRows); ?></span> of <span
                            class="font-medium"><?php echo $totalRows; ?></span> results
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                class="px-3 py-1 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                class="px-3 py-1 rounded-lg text-sm border <?php echo $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                class="px-3 py-1 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.booking-checkbox');
        const deleteBtn = document.getElementById('delete-btn');

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.booking-checkbox:checked').length;
            if (checkedCount > 0) {
                deleteBtn.classList.remove('hidden');
                deleteBtn.innerHTML = `<i class="fas fa-trash-alt"></i> Delete (${checkedCount})`;
            } else {
                deleteBtn.classList.add('hidden');
            }
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateDeleteButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateDeleteButton);
        });

        function confirmDelete() {
            if (confirm('Are you sure you want to delete these bookings? This action cannot be undone.')) {
                document.getElementById('bulk-form').submit();
            }
        }
    </script>
</body>

</html>