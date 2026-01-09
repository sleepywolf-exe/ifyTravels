<?php require 'auth_check.php';
$db = Database::getInstance();

// Auto-fix: Check if columns exist, if not, add them (Silent Migration)
try {
    // Check if 'status' column exists
    $check = $db->fetch("SHOW COLUMNS FROM inquiries LIKE 'status'");
    if (!$check) {
        $db->execute("ALTER TABLE inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'new'");
        $db->execute("ALTER TABLE inquiries ADD COLUMN admin_notes TEXT");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_source VARCHAR(255)");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_medium VARCHAR(255)");
        $db->execute("ALTER TABLE inquiries ADD COLUMN utm_campaign VARCHAR(255)");
    }
} catch (Exception $e) {
    // Suppress error if already exists or permission issue
}

$inquiries = $db->fetchAll("SELECT * FROM inquiries ORDER BY created_at DESC"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inquiries - Admin</title>
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
    <main class="flex-1 overflow-y-auto p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Inquiries</h1>
            <div class="text-sm text-gray-500">
                Manage your leads and customer questions
            </div>
        </header>

        <!-- Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                <?php echo $_SESSION['flash_message'];
                unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Inquiries List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <?php if (empty($inquiries)): ?>
                <div class="p-12 text-center text-gray-500">
                    <p>No inquiries found yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Type / Source</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($inquiries as $i): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, H:i', strtotime($i['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800"><?php echo e($i['name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($i['email']); ?></div>
                                        <?php if (!empty($i['phone'])): ?>
                                            <div class="text-xs text-gray-500"><?php echo e($i['phone']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-800 truncate max-w-xs"
                                            title="<?php echo e($i['subject']); ?>">
                                            <?php echo e($i['subject']); ?>
                                        </div>
                                        <?php if (!empty($i['utm_source'])): ?>
                                            <div class="mt-1 flex gap-1 flex-wrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Source: <?php echo e($i['utm_source']); ?>
                                                </span>
                                                <?php if (!empty($i['utm_campaign'])): ?>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        Cmp: <?php echo e($i['utm_campaign']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $statusColors = [
                                            'new' => 'bg-green-100 text-green-800',
                                            'contacted' => 'bg-yellow-100 text-yellow-800',
                                            'converted' => 'bg-blue-100 text-blue-800',
                                            'closed' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $status = $i['status'] ?? 'new';
                                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                        <button onclick='openModal(<?php echo json_encode($i); ?>)'
                                            class="text-primary hover:text-teal-700 mr-3">View/Edit</button>

                                        <form method="POST" action="inquiry_actions.php" class="inline-block"
                                            onsubmit="return confirm('Delete this inquiry?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $i['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Details/Edit Modal -->
    <div id="inquiryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="inquiry_actions.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="modalId">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">Inquiry Details
                        </h3>

                        <div class="space-y-4">
                            <!-- Read Only Info -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block text-gray-500 text-xs uppercase">Name</label>
                                    <p id="modalName" class="font-medium text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-gray-500 text-xs uppercase">Email</label>
                                    <p id="modalEmail" class="font-medium text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-gray-500 text-xs uppercase">Phone</label>
                                    <p id="modalPhone" class="font-medium text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-gray-500 text-xs uppercase">Date</label>
                                    <p id="modalDate" class="font-medium text-gray-800"></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-500 text-xs uppercase mb-1">Message</label>
                                <div id="modalMessage"
                                    class="bg-gray-50 p-3 rounded text-sm text-gray-700 whitespace-pre-wrap max-h-32 overflow-y-auto">
                                </div>
                            </div>

                            <hr>

                            <!-- Editable Fields -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="modalStatus"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="converted">Converted</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                <textarea name="admin_notes" id="modalNotes" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                                    placeholder="Add internal notes here..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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