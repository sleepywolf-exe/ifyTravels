<?php
require 'auth_check.php';
$db = Database::getInstance();

// Fetch Packages for Dropdown
$packages = $db->fetchAll("SELECT id, title, price FROM packages ORDER BY title ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Booking - Admin</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/admin-favicon.png'); ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen">
    <?php include 'sidebar_inc.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Booking</h1>
                <p class="text-gray-500 mt-1">Manually record a booking for a customer</p>
            </div>
            <a href="bookings.php" class="text-gray-500 hover:text-gray-700 font-medium">
                &larr; Back to List
            </a>
        </header>

        <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="booking_actions.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <!-- Package Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Package</label>
                    <select name="package_id" id="package_select" onchange="updatePrice()"
                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 bg-gray-50">
                        <option value="">-- Custom / No Package --</option>
                        <?php foreach ($packages as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>"
                                data-title="<?php echo htmlspecialchars($p['title']); ?>">
                                <?php echo htmlspecialchars($p['title']); ?> (Approx. ₹
                                <?php echo number_format($p['price']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="package_name" id="package_name_input">
                </div>

                <!-- Customer Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name</label>
                        <input type="text" name="customer_name" required
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-gray-50 placeholder-gray-400"
                            placeholder="e.g. Rahul Sharma">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" required
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-gray-50 placeholder-gray-400"
                            placeholder="+91 99999 99999">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-gray-50 placeholder-gray-400"
                            placeholder="rahul@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Travel Date</label>
                        <input type="date" name="travel_date" required
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-gray-50">
                    </div>
                </div>

                <!-- Financials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Price (₹)</label>
                        <input type="number" name="total_price" id="total_price" step="0.01" required
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-gray-50 font-mono"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-3 bg-gray-50">
                            <option value="Confirmed">✅ Confirmed</option>
                            <option value="Pending">⏳ Pending</option>
                            <option value="Cancelled">❌ Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requests / Notes</label>
                    <textarea name="special_requests" rows="3"
                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 p-4 bg-gray-50"
                        placeholder="Any special requirements..."></textarea>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5">
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function updatePrice() {
            const select = document.getElementById('package_select');
            const priceInput = document.getElementById('total_price');
            const nameInput = document.getElementById('package_name_input');

            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                // Auto-fill price and name if package selected
                priceInput.value = selectedOption.getAttribute('data-price');
                nameInput.value = selectedOption.getAttribute('data-title');
            } else {
                // Allow manual entry
                nameInput.value = 'Custom Booking';
            }
        }
    </script>
</body>

</html>