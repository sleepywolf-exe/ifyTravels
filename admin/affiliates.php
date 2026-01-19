<?php
require 'auth_check.php';

$db = Database::getInstance();

// Handle Status Toggle
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $current = $_GET['current'];
    $newStatus = ($current === 'active') ? 'inactive' : 'active';

    $db->execute("UPDATE affiliates SET status = ? WHERE id = ?", [$newStatus, $id]);
    redirect('affiliates.php');
}

// Handle Create Affiliate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_affiliate'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $code = strtoupper(trim($_POST['code']));

    // Validations
    if (empty($name) || empty($email) || empty($code)) {
        $error = "All fields are required.";
    } else {
        // Check uniqueness
        $exists = $db->fetch("SELECT id FROM affiliates WHERE code = ? OR email = ?", [$code, $email]);
        if ($exists) {
            $error = "Affiliate code or email already exists.";
        } else {
            $db->execute("INSERT INTO affiliates (name, email, code, status) VALUES (?, ?, ?, 'active')", [$name, $email, $code]);
            redirect('affiliates.php?success=created');
        }
    }
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
    </style>
</head>

<body class="bg-gray-50 flex h-screen">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Affiliate Partners</h1>
            <button onclick="openModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition flex items-center gap-2 shadow-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add New Affiliate
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Create Modal -->
        <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form method="POST" class="p-6">
                            <input type="hidden" name="create_affiliate" value="1">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4" id="modal-title">Add New Affiliate</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Partner Name</label>
                                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm border px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm border px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Affiliate Code</label>
                                    <input type="text" name="code" placeholder="e.g. SUMMER25" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm border px-3 py-2 uppercase">
                                    <p class="text-xs text-gray-500 mt-1">Must be unique. Will be converted to uppercase.</p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" onclick="closeModal()" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:text-sm">Cancel</button>
                                <button type="submit" class="rounded-md border border-transparent bg-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none sm:text-sm">Create Partner</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openModal() { document.getElementById('createModal').classList.remove('hidden'); }
            function closeModal() { document.getElementById('createModal').classList.add('hidden'); }
        </script>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                            <th class="p-4">ID</th>
                            <th class="p-4">Partner Name</th>
                            <th class="p-4">Affiliate Code</th>
                            <th class="p-4">Email</th>
                            <th class="p-4 text-center">Bookings</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4">Registered</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php if (empty($affiliates)): ?>
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400">
                                    No affiliates found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($affiliates as $aff): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 text-gray-400">#
                                        <?php echo $aff['id']; ?>
                                    </td>
                                    <td class="p-4 font-medium text-gray-800">
                                        <?php echo e($aff['name']); ?>
                                    </td>
                                    <td class="p-4">
                                        <code
                                            class="bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-100 text-xs font-bold font-mono">
                                                            <?php echo e($aff['code']); ?>
                                                        </code>
                                    </td>
                                    <td class="p-4 text-gray-600">
                                        <?php echo e($aff['email']); ?>
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
                                        <?php echo date('M j, Y h:i A', strtotime($aff['created_at'])); ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="?toggle_status=1&id=<?php echo $aff['id']; ?>&current=<?php echo $aff['status']; ?>"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border transition <?php echo $aff['status'] === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50'; ?>">
                                            <?php echo $aff['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>