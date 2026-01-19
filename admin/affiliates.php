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
            <!-- Optional: Add New Affiliate Button -->
        </div>

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