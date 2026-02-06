<?php
require 'auth_check.php';
$db = Database::getInstance();

// Auto-fix: Check if columns exist (Silent Migration)
try {
    $check = $db->fetch("SHOW COLUMNS FROM inquiries LIKE 'status'");
    if (!$check) {
        $db->execute("ALTER TABLE inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'new'");
        $db->execute("ALTER TABLE inquiries ADD COLUMN admin_notes TEXT");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_source VARCHAR(255)");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_medium VARCHAR(255)");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_campaign VARCHAR(255)");
    }
} catch (Exception $e) {
}

// 1. Handle Filters
$statusFilter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$sourceFilter = isset($_GET['source']) ? sanitize_input($_GET['source']) : '';

// 2. Handle Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 3. Build Query
$sql = "SELECT * FROM inquiries WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if (!empty($sourceFilter)) {
    $sql .= " AND utm_source LIKE ?";
    $params[] = "%$sourceFilter%";
}

// 4. Get Total Count
$countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$totalRows = $db->fetch($countSql, $params)['total'] ?? 0;
$totalPages = ceil($totalRows / $limit);

// 5. Fetch Data
// Use ID for sorting as it is safer than created_at which might differ across envs
$sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$inquiries = $db->fetchAll($sql, $params);

// Get unique sources for dropdown
$sources = $db->fetchAll("SELECT DISTINCT utm_source FROM inquiries WHERE utm_source IS NOT NULL AND utm_source != ''");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inquiries - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen text-gray-800">
    <?php include 'sidebar_inc.php'; ?>
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 relative z-0">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Inquiries</h1>
                <p class="text-gray-500 mt-2 text-lg font-light">Manage your leads and customer questions</p>
            </div>

            <!-- Filters & Search -->
            <form class="flex flex-wrap gap-3 bg-white p-2 rounded-xl border border-gray-100 shadow-sm" method="GET">
                <select name="status"
                    class="bg-gray-50 border-0 rounded-lg text-sm text-gray-600 font-medium focus:ring-2 focus:ring-blue-100 py-2.5 pl-3 pr-8 w-36">
                    <option value="">All Status</option>
                    <option value="new" <?php echo $statusFilter == 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="contacted" <?php echo $statusFilter == 'contacted' ? 'selected' : ''; ?>>Contacted
                    </option>
                    <option value="converted" <?php echo $statusFilter == 'converted' ? 'selected' : ''; ?>>Converted
                    </option>
                    <option value="closed" <?php echo $statusFilter == 'closed' ? 'selected' : ''; ?>>Closed</option>
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
                    <a href="inquiries.php"
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

        <!-- Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div
                class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl mb-8 shadow-sm flex items-center border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?php echo $_SESSION['flash_message'];
                unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Inquiries List -->
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
            <?php if (empty($inquiries)): ?>
                <div class="p-20 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                        📩</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No Inquiries Found</h3>
                    <p class="text-gray-500">Try adjusting your filters.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">User Details
                                </th>
                                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Subject &
                                    Source</th>
                                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($inquiries as $i): ?>
                                <tr class="hover:bg-blue-50/50 transition duration-150 group">
                                    <td class="px-8 py-6 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($i['created_at'])); ?>
                                        <span
                                            class="block text-xs text-gray-400 mt-1"><?php echo date('h:i A', strtotime($i['created_at'])); ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm mr-4">
                                                <?php echo strtoupper(substr($i['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 truncate max-w-[200px]"
                                                    title="<?php echo e($i['name']); ?>"><?php echo e($i['name']); ?></div>
                                                <div class="text-sm text-gray-500 truncate max-w-[200px]"
                                                    title="<?php echo e($i['email']); ?>"><?php echo e($i['email']); ?></div>
                                                <?php if (!empty($i['phone'])): ?>
                                                    <div class="text-xs text-gray-400 mt-0.5"><?php echo e($i['phone']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-[250px]"
                                            title="<?php echo e($i['subject']); ?>">
                                            <?php echo e($i['subject']); ?>
                                        </div>
                                        <!-- Source Icon -->
                                        <?php if (!empty($i['utm_source'])): ?>
                                            <div class="mt-2 flex items-center"
                                                title="Source: <?php echo ucfirst($i['utm_source']); ?>">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center shadow-sm">
                                                    <?php echo get_source_icon_svg($i['utm_source']); ?>
                                                </div>
                                                <span
                                                    class="ml-2 text-xs text-gray-400 font-medium capitalize"><?php echo e($i['utm_source']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php
                                        $statusColors = [
                                            'new' => 'bg-green-100 text-green-700 border-green-200',
                                            'contacted' => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'converted' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'closed' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        ];
                                        $status = $i['status'] ?? 'new';
                                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $color; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right text-sm font-medium ">
                                        <button onclick='openModal(<?php echo json_encode($i); ?>)'
                                            class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold transition hover:underline">View
                                            Details</button>

                                        <form method="POST" action="inquiry_actions.php" class="inline-block"
                                            onsubmit="return confirm('Delete this inquiry?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $i['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-8 py-5 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span
                                class="font-medium"><?php echo min($offset + $limit, $totalRows); ?></span> of <span
                                class="font-medium"><?php echo $totalRows; ?></span> inquiries
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition font-medium">Previous</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                    class="px-4 py-2 rounded-xl text-sm font-medium border <?php echo $i == $page ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200' : 'border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&source=<?php echo $sourceFilter; ?>"
                                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition font-medium">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </main>

    <!-- Details/Edit Modal (Premium UX) -->
    <div id="inquiryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Backdrop with Blur -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
            onclick="closeModal()"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">

                <form action="inquiry_actions.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="modalId">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <!-- Header -->
                    <div
                        class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white tracking-wide" id="modalTitle">Inquiry Details</h3>
                        </div>
                        <button type="button" onclick="closeModal()" class="text-white/70 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 sm:p-8">
                        <div class="space-y-6">
                            <!-- Read Only Info Grid -->
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Customer
                                        Name</label>
                                    <p id="modalName" class="text-lg font-semibold text-gray-900 break-words"></p>
                                </div>
                                <div class="col-span-1 text-right">
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date
                                        Received</label>
                                    <p id="modalDate" class="text-sm font-medium text-gray-600"></p>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email
                                        Address</label>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p id="modalEmail" class="text-sm text-gray-700 font-medium break-all"></p>
                                    </div>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Phone
                                        Number</label>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C7.82 21 2 15.18 2 7V5z">
                                            </path>
                                        </svg>
                                        <p id="modalPhone" class="text-sm text-gray-700 font-medium"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Box -->
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 relative">
                                <div
                                    class="absolute -top-2 left-4 bg-white px-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Message</div>
                                <div id="modalMessage"
                                    class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap max-h-40 overflow-y-auto italic">
                                </div>
                            </div>

                            <hr class="border-gray-100 my-4">

                            <!-- Editable Fields Grid -->
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status</label>
                                    <div class="relative">
                                        <select name="status" id="modalStatus"
                                            class="block w-full pl-4 pr-10 py-3 text-base border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-50 transition-colors hover:bg-white border">
                                            <option value="new">🆕 New Inquiry</option>
                                            <option value="contacted">📞 Contacted</option>
                                            <option value="converted">✅ Converted (Won)</option>
                                            <option value="closed">❌ Closed (Lost)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Internal Notes</label>
                                    <textarea name="admin_notes" id="modalNotes" rows="3"
                                        class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-200 rounded-lg p-3 bg-gray-50 focus:bg-white transition-all placeholder-gray-400"
                                        placeholder="Write private notes for your team here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="bg-gray-50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-blue-500/30 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-base font-semibold text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-all transform hover:scale-[1.02]">
                            Save Changes
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                            onclick="closeModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(data) {
            document.getElementById('inquiryModal').classList.remove('hidden');
            document.getElementById('modalId').value = data.id;
            document.getElementById('modalTitle').textContent = data.subject ? data.subject : 'Inquiry Details';

            document.getElementById('modalName').textContent = data.name;
            document.getElementById('modalEmail').textContent = data.email;
            document.getElementById('modalPhone').textContent = data.phone || '-';
            document.getElementById('modalDate').textContent = new Date(data.created_at).toLocaleString();
            document.getElementById('modalMessage').textContent = data.message;

            document.getElementById('modalStatus').value = data.status || 'new';
            document.getElementById('modalNotes').value = data.admin_notes || '';
        }

        function closeModal() {
            document.getElementById('inquiryModal').classList.add('hidden');
        }
    </script>
</body>

</html>