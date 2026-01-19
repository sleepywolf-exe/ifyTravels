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
            <a href="affiliates_create.php"
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition flex items-center gap-2 shadow-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Add New Affiliate
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></div>
        <?php endif; ?>



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
                                    <td class="p-4 font-medium text-gray-800 group-hover:text-primary transition-colors">
                                        <a href="affiliate_details.php?id=<?php echo $aff['id']; ?>"
                                            class="flex items-center gap-2">
                                            <?php echo e($aff['name']); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity text-gray-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
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

                                        <a href="affiliate_details.php?id=<?php echo $aff['id']; ?>"
                                            class="ml-2 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                            View
                                        </a> <button
                                            onclick="copyAffiliateLink('<?php echo base_url('?ref=' . $aff['code']); ?>')"
                                            class="ml-2 text-xs font-medium px-3 py-1.5 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition"
                                            title="Copy Affiliate Link">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                            Copy Link
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

    <script>
        function copyAffiliateLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                // Show a quick toast or alert
                // Simple alert for now, could be improved with a custom toast
                const el = document.createElement('div');
                el.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50 animate-bounce';
                el.innerText = 'Link Copied: ' + url;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy link. Please manually copy: ' + url);
            });
        }
    </script>
</body>

</html>