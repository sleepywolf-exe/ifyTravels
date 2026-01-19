<?php
require 'auth_check.php';

$db = Database::getInstance();
$id = $_GET['id'] ?? null;

if (!$id) {
    redirect('bookings.php');
}

// Handle Form Submission (Update Status/Notes)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $notes = $_POST['admin_notes'];

    $db->execute("UPDATE bookings SET status = ?, admin_notes = ? WHERE id = ?", [$status, $notes, $id]);
    $message = "Booking updated successfully!";
}

// Fetch Booking Details (with Package Name joined optionally or just separate)
// Wait, previous bookings query used package_name? Let's check `bookings.php` source again.
// Oops, `bookings.php` used `SELECT * FROM bookings` but assumed `package_name` exists.
// Actually `bookings` table usually has `package_id`. `package_name` was likely populated if the table has it or joined.
// `submit_booking.php` inserts `package_name`? No, schema says `package_id`.
// Let's assume `bookings` table has `package_id`.
// I should JOIN packages table to get name, OR fetch package separately.
// Wait, `submit_booking.php` MIGHT have inserted package name if schema was loose?
// Re-reading `bookings.php` line 39: `echo e($b['package_name']);`
// Re-reading schema: `package_id INTEGER` ... no `package_name` column.
// So `bookings.php` might be failing or the code snippet I saw earlier had a JOIN I missed?
// In `bookings.php`: `$bookings = Database::getInstance()->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC");`
// If schema only has `package_id`, `$b['package_name']` would be undefined.
// I will investigate this but for now I will fix it by doing a JOIN in `booking-details.php` to be safe.

// New Query with Join
$booking = $db->fetch("
    SELECT b.*, p.title as package_title, p.price as package_price,
           a.name as affiliate_name, a.code as affiliate_code
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    LEFT JOIN affiliates a ON b.affiliate_id = a.id
    WHERE b.id = ?
", [$id]);

if (!$booking) {
    die("Booking not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking #
        <?php echo $id; ?> - Admin
    </title>
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
            <h1 class="text-3xl font-bold text-gray-800">Booking Details #
                <?php echo $id; ?>
            </h1>
            <a href="bookings.php" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to List
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Customer & Package Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Customer Information</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500 font-medium">Full Name</dt>
                            <dd class="text-gray-900 font-semibold">
                                <?php echo e($booking['customer_name']); ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 font-medium">Email Address</dt>
                            <dd class="text-gray-900">
                                <?php echo e($booking['email']); ?>
                                <a href="mailto:<?php echo e($booking['email']); ?>"
                                    class="text-blue-500 hover:underline text-xs ml-1">(Email)</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 font-medium">Phone Number</dt>
                            <dd class="text-gray-900">
                                <?php echo e($booking['phone']); ?>
                                <a href="tel:<?php echo e($booking['phone']); ?>"
                                    class="text-blue-500 hover:underline text-xs ml-1">(Call)</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 font-medium">Travel Date</dt>
                            <dd class="text-gray-900 font-semibold">
                                <?php echo e($booking['travel_date']); ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Package Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Package Details</h2>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <dt class="text-sm text-gray-500 font-medium">Selected Package</dt>
                            <dd class="text-lg font-bold text-primary">
                                <?php echo e($booking['package_title'] ?? 'Unknown Package (ID: ' . $booking['package_id'] . ')'); ?>
                            </dd>
                        </div>
                        <div class="text-right">
                            <dt class="text-sm text-gray-500 font-medium">Estimated Price</dt>
                            <dd class="text-xl font-bold text-gray-800">₹
                                <?php echo number_format($booking['package_price'] ?? 0); ?>
                            </dd>
                            <span class="text-xs text-gray-400">per person</span>
                        </div>
                    </div>

                    <?php if (!empty($booking['affiliate_id'])): ?>
                        <div class="mt-4 pt-4 border-t">
                            <dt class="text-sm text-gray-500 font-medium mb-1">Referred By (Affiliate)</dt>
                            <dd class="flex items-center gap-2">
                                <span
                                    class="font-semibold text-gray-800"><?php echo e($booking['affiliate_name']); ?></span>
                                <span
                                    class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded border border-blue-100 font-mono">
                                    <?php echo e($booking['affiliate_code']); ?>
                                </span>
                            </dd>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <dt class="text-sm text-gray-500 font-medium mb-1">Special Requests</dt>
                        <dd class="bg-gray-50 p-4 rounded-lg text-gray-700 text-sm whitespace-pre-wrap italic">
                            <?php echo e($booking['special_requests'] ?: 'No special requests provided.'); ?>
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Right Column: CRM Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Manage Booking</h2>

                    <!-- Actions -->
                    <div class="mb-6">
                        <a href="../services/generate_voucher.php?id=<?php echo $id; ?>" target="_blank"
                            class="w-full flex items-center justify-center gap-2 bg-emerald-50 text-emerald-700 font-bold py-3 rounded-lg hover:bg-emerald-100 transition border border-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Invoice PDF
                        </a>
                    </div>

                    <form method="POST" class="space-y-6">
                        <!-- Status Update -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Booking Status</label>
                            <select name="status"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                                <?php
                                $statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
                                foreach ($statuses as $s):
                                    ?>
                                    <option value="<?php echo $s; ?>" <?php echo $booking['status'] === $s ? 'selected' : ''; ?>>
                                        <?php echo $s; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Admin Notes
                                <span class="block text-xs font-normal text-gray-500">Internal notes, private to
                                    admin.</span>
                            </label>
                            <textarea name="admin_notes" rows="6"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none"
                                placeholder="E.g. Called customer on 12th Jan, verified dates. Waiting for payment."><?php echo e($booking['admin_notes'] ?? ''); ?></textarea>
                        </div>

                        <!-- History / Timestamp -->
                        <div class="text-xs text-gray-400 border-t pt-4">
                            Booking Created:
                            <?php echo $booking['created_at']; ?>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>