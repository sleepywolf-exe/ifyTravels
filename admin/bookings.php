<?php require 'auth_check.php';
$bookings = Database::getInstance()->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bookings - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen"><?php include 'sidebar_inc.php'; ?>
    <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Bookings</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Ref ID</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Package</th>
                        <th class="px-6 py-3">Travel Date</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100"><?php foreach ($bookings as $b): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono text-xs">#<?php echo $b['id']; ?></td>
                            <td class="px-6 py-3">
                                <div class="font-bold text-gray-800"><?php echo e($b['customer_name']); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($b['email']); ?></div>
                            </td>
                            <td class="px-6 py-3"><?php echo e($b['package_name']); ?></td>
                            <td class="px-6 py-3"><?php echo e($b['travel_date']); ?></td>
                            <td class="px-6 py-3"><span
                                    class="inline-block px-2 py-1 rounded text-xs font-semibold <?php echo $b['status'] == 'Confirmed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'; ?>"><?php echo e($b['status']); ?></span>
                            </td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>