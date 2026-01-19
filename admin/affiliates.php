<?php
require 'auth_check.php';

$db = Database::getInstance();

// Handle AJAX Requests (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $commission = isset($_POST['commission']) ? floatval($_POST['commission']) : 10.00;
    $password = $_POST['password'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if (empty($name) || empty($email) || empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Name, Email, and Code are required.']);
        exit;
    }

    // Check Uniqueness (excluding current ID on update)
    $sql = "SELECT id FROM affiliates WHERE (code = ? OR email = ?) AND id != ?";
    $exists = $db->fetch($sql, [$code, $email, $id ?: 0]);

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Code or Email already exists.']);
        exit;
    }

    if ($action === 'create') {
        // Require password for new accounts
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Password is required for new partners.']);
            exit;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($db->execute("INSERT INTO affiliates (name, email, code, status, commission_rate, password_hash) VALUES (?, ?, ?, 'active', ?, ?)", [$name, $email, $code, $commission, $hash])) {
            $newId = $db->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Affiliate created successfully',
                'affiliate' => [
                    'id' => $newId,
                    'name' => $name,
                    'email' => $email,
                    'code' => $code,
                    'booking_count' => 0,
                    'status' => 'active',
                    'commission_rate' => $commission,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insert failed.']);
        }
    } elseif ($action === 'update' && $id) {
        // Password update is optional
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->execute("UPDATE affiliates SET name = ?, email = ?, code = ?, commission_rate = ?, password_hash = ? WHERE id = ?", [$name, $email, $code, $commission, $hash, $id]);
        } else {
            $db->execute("UPDATE affiliates SET name = ?, email = ?, code = ?, commission_rate = ? WHERE id = ?", [$name, $email, $code, $commission, $id]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Affiliate updated successfully',
            'affiliate' => [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'code' => $code,
                'commission_rate' => $commission
            ]
        ]);
    } elseif ($action === 'delete' && $id) {
        // preserve history (set id null) or delete?
        // User requested DELETE functionality. Safe way: NULLify bookings, Delete affiliate.
        $db->execute("UPDATE bookings SET affiliate_id = NULL WHERE affiliate_id = ?", [$id]);
        if ($db->execute("DELETE FROM affiliates WHERE id = ?", [$id])) {
            echo json_encode(['success' => true, 'message' => 'Affiliate deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database delete failed.']);
        }
    }
    exit;
}

// Handle Status Toggle (Legacy GET)
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $current = $_GET['current'];
    $newStatus = ($current === 'active') ? 'inactive' : 'active';
    $db->execute("UPDATE affiliates SET status = ? WHERE id = ?", [$newStatus, $id]);
    redirect('affiliates.php');
}

// Fetch Affiliates
$query = "SELECT a.*, (SELECT COUNT(id) FROM bookings WHERE affiliate_id = a.id) as booking_count 
          FROM affiliates a ORDER BY created_at DESC";
$affiliates = $db->fetchAll($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Affiliates</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Slide-over animation */
        .slide-over {
            transition: transform 0.3s ease-in-out;
        }

        .slide-over-open {
            transform: translateX(0);
        }

        .slide-over-closed {
            transform: translateX(100%);
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 relative">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Affiliate Partners</h1>
            <button onclick="openPanel('create')"
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition flex items-center gap-2 shadow-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Add New Affiliate
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="affiliatesTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                            <th class="p-4">ID</th>
                            <th class="p-4">Partner Name</th>
                            <th class="p-4">Affiliate Code</th>
                            <th class="p-4">Email</th>
                            <th class="p-4 text-center">Commission</th>
                            <th class="p-4 text-center">Bookings</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4">Registered</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php if (empty($affiliates)): ?>
                            <tr id="emptyRow">
                                <td colspan="9" class="p-8 text-center text-gray-400">No affiliates found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($affiliates as $aff): ?>
                                <tr class="hover:bg-gray-50 transition" id="row-<?php echo $aff['id']; ?>">
                                    <td class="p-4 text-gray-400">#<?php echo $aff['id']; ?></td>
                                    <td class="p-4 font-medium text-gray-800 group-hover:text-primary transition-colors">
                                        <a href="affiliate_details.php?id=<?php echo $aff['id']; ?>"
                                            class="flex items-center gap-2" id="name-<?php echo $aff['id']; ?>">
                                            <?php echo e($aff['name']); ?>
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <code
                                            class="bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-100 text-xs font-bold font-mono"
                                            id="code-<?php echo $aff['id']; ?>"><?php echo e($aff['code']); ?></code>
                                    </td>
                                    <td class="p-4 text-gray-600" id="email-<?php echo $aff['id']; ?>">
                                        <?php echo e($aff['email']); ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-bold">
                                            <?php echo number_format($aff['commission_rate'], 1); ?>%
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700">
                                        <?php echo $aff['booking_count']; ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $aff['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($aff['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-500 text-xs">
                                        <?php echo date('M j, Y', strtotime($aff['created_at'])); ?>
                                    </td>
                                    <td class="p-4 text-center flex justify-center items-center gap-2">
                                        <a href="?toggle_status=1&id=<?php echo $aff['id']; ?>&current=<?php echo $aff['status']; ?>"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border transition <?php echo $aff['status'] === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50'; ?>">
                                            <?php echo $aff['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </a>

                                        <button
                                            onclick="openPanel('update', {id: <?php echo $aff['id']; ?>, name: '<?php echo addslashes($aff['name']); ?>', email: '<?php echo addslashes($aff['email']); ?>', code: '<?php echo addslashes($aff['code']); ?>', commission: '<?php echo $aff['commission_rate'] ?? 10.00; ?>'})"
                                            class="text-xs font-medium px-2 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <a href="affiliate_details.php?id=<?php echo $aff['id']; ?>"
                                            class="text-xs font-medium px-2 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                                            title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <button onclick="copyAffiliateLink('<?php echo base_url('?ref=' . $aff['code']); ?>')"
                                            class="text-xs font-medium px-2 py-1.5 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition"
                                            title="Copy Link">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Slide-over Panel Backdrop -->
    <div id="panelBackdrop" onclick="closePanel()"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden transition-opacity"></div>

    <!-- Slide-over Panel -->
    <div id="sidePanel"
        class="fixed inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl z-50 slide-over slide-over-closed flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 id="panelTitle" class="text-xl font-bold text-gray-800">Add New Affiliate</h2>
            <button onclick="closePanel()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto">
            <form id="affiliateForm" onsubmit="saveAffiliate(event)" class="space-y-6">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="affiliateId" value="">

                <div id="formError" class="hidden bg-red-50 text-red-600 p-3 rounded-lg border border-red-100 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Partner Name</label>
                    <input type="text" name="name" id="affName" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                        placeholder="e.g. John Doe">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="affEmail" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                        placeholder="partner@example.com">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Commission Rate (%)</label>
                    <input type="number" step="0.01" name="commission" id="affCommission" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                        placeholder="10.00" value="10.00">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="affPassword"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                        placeholder="Leave blank to keep unchanged">
                    <p class="text-xs text-gray-500 mt-1" id="passwordHelp">Required for new partners.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Affiliate Code</label>
                    <div class="flex gap-2">
                        <input type="text" name="code" id="affCode" required
                            class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition uppercase"
                            placeholder="e.g. SUMMER25">
                        <button type="button" onclick="generateRandomCode()"
                            class="bg-gray-100 px-4 rounded-lg font-medium text-gray-600 hover:bg-gray-200 transition text-sm">
                            Random
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Must be unique. Will be converted to uppercase.</p>
                </div>
            </form>
        </div>

        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <button type="submit" form="affiliateForm" id="saveBtn"
                class="w-full bg-teal-600 text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition shadow-lg flex justify-center items-center">
                <span>Save Partner</span>
            </button>
        </div>
    </div>

    <script>
        const panel = document.getElementById('sidePanel');
        const backdrop = document.getElementById('panelBackdrop');
        const form = document.getElementById('affiliateForm');
        const errorDiv = document.getElementById('formError');

        function openPanel(mode, data = {}) {
            // Reset form
            form.reset();
            errorDiv.classList.add('hidden');

            if (mode === 'update') {
                document.getElementById('panelTitle').innerText = 'Edit Affiliate';
                document.getElementById('formAction').value = 'update';
                document.getElementById('affiliateId').value = data.id;
                document.getElementById('affName').value = data.name;
                document.getElementById('affEmail').value = data.email;
                document.getElementById('affCode').value = data.code;
                document.getElementById('affCommission').value = data.commission || 10.00;
                document.getElementById('affPassword').required = false;
                document.getElementById('passwordHelp').innerText = 'Leave blank to keep current password.';
                document.getElementById('saveBtn').innerText = 'Update Partner';
            } else {
                document.getElementById('panelTitle').innerText = 'Add New Affiliate';
                document.getElementById('formAction').value = 'create';
                document.getElementById('affiliateId').value = '';
                document.getElementById('affCommission').value = '10.00';
                document.getElementById('affPassword').required = true;
                document.getElementById('passwordHelp').innerText = 'Required for new partners.';
                document.getElementById('saveBtn').innerText = 'Create Partner';
            }

            // Show Panel
            backdrop.classList.remove('hidden');
            panel.classList.remove('slide-over-closed');
            panel.classList.add('slide-over-open');
        }

        function closePanel() {
            backdrop.classList.add('hidden');
            panel.classList.remove('slide-over-open');
            panel.classList.add('slide-over-closed');
        }

        function copyAffiliateLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const el = document.createElement('div');
                el.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50 animate-bounce';
                el.innerText = 'Link Copied: ' + url;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 2000);
            }).catch(err => {
                alert('Copy failed: ' + url);
            });
        }

        function saveAffiliate(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const btn = document.getElementById('saveBtn');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            btn.disabled = true;

            fetch('affiliates.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Simple reload to refresh table, or could use DOM manipulation for smoother feel
                    } else {
                        errorDiv.innerText = data.message;
                        errorDiv.classList.remove('hidden');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    errorDiv.innerText = "An unexpected error occurred.";
                    errorDiv.classList.remove('hidden');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }
        function generateRandomCode() {
            const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('affCode').value = code;
        }

        function confirmDelete(id) {
            if (!confirm('Are you sure you want to delete this affiliate? This action cannot be undone.')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('affiliates.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from DOM
                        const row = document.getElementById('row-' + id);
                        if (row) row.remove();
                        // Optional: Check if table empty
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => alert('An unexpected error occurred.'));
        }
    </script>
</body>

</html>