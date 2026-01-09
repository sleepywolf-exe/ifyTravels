<?php
$pageTitle = "Book Your Trip";
include __DIR__ . '/../includes/functions.php';

$pkgId = $_GET['packageId'] ?? null;
$selectedPkg = null;
if ($pkgId) {
    $selectedPkg = getPackageById($pkgId);
}

// Handle POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $date = $_POST['date'] ?? '';
    $requests = $_POST['requests'] ?? '';
    $pkgIdPost = $_POST['package_id'] ?? null;
    $pkgNamePost = $_POST['package_name'] ?? '';

    // Insert into DB
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_name, email, phone, travel_date, special_requests, package_id, package_name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', datetime('now'))");
        // Handled null package_id properly
        $pid = is_numeric($pkgIdPost) ? $pkgIdPost : null;

        $stmt->execute([$name, $email, $phone, $date, $requests, $pid, $pkgNamePost]);

        $bookingId = $pdo->lastInsertId();

        header('Location: booking-success.php?id=' . $bookingId);
        exit;
    } catch (Exception $e) {
        $error = "Booking failed. Please try again.";
    }
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Link Glassmorphism CSS -->
<link rel="stylesheet" href="../assets/css/glassmorphism.css">

<!-- Header (Dynamic) -->
<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[300px] flex items-center justify-center"
    style="background-image: url('<?php echo base_url('assets/images/destinations/maldives.jpg'); ?>');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl font-bold text-white mb-4">Start Your Journey</h1>
        <p class="text-gray-100 max-w-2xl mx-auto">Fill in your details to secure your dream vacation.</p>
    </div>
</div>

<div class="container mx-auto px-6 py-12 flex-1">
    <div
        class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col md:flex-row">

        <!-- Summary Sidebar -->
        <div class="md:w-1/3 bg-gray-50 p-8 border-r border-gray-100">
            <h3 class="font-bold text-lg mb-6">Booking Summary</h3>

            <?php if ($selectedPkg): ?>
                <div id="package-summary">
                    <img id="pkg-img" src="<?php echo base_url($selectedPkg['image']); ?>"
                        class="w-full h-32 object-cover rounded-lg mb-4 shadow-sm">
                    <h4 id="pkg-title" class="font-bold text-charcoal mb-1">
                        <?php echo htmlspecialchars($selectedPkg['title']); ?>
                    </h4>
                    <p id="pkg-dest" class="text-sm text-gray-500 mb-4"><?php echo $selectedPkg['duration']; ?></p>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                        <span class="text-gray-600">Total Price</span>
                        <span id="pkg-price"
                            class="font-bold text-primary text-xl">₹<?php echo $selectedPkg['price']; ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div id="no-package-msg" class="flex flex-col items-center justify-center h-full text-center py-10">
                    <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-2">No Package Selected</h4>
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                        You can fill out this form for a general inquiry, or browse our packages to book a specific tour.
                    </p>
                    <a href="<?php echo base_url('packages'); ?>"
                        class="inline-block bg-white border border-primary text-primary px-6 py-2 rounded-lg font-bold hover:bg-primary hover:text-white transition shadow-sm">
                        Browse Packages
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form -->
        <div class="md:w-2/3 p-8 glass-form" style="background: linear-gradient(135deg, #0F766E 0%, #0d9488 100%);">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="booking-form" method="POST" class="space-y-6">
                <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($pkgId); ?>">
                <input type="hidden" name="package_name"
                    value="<?php echo $selectedPkg ? htmlspecialchars($selectedPkg['title']) : 'Custom Inquiry'; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="glass-label">Full Name</label>
                        <input type="text" name="name" required placeholder="Enter your full name"
                            class="glass-input w-full">
                    </div>
                    <div>
                        <label class="glass-label">Email Address</label>
                        <input type="email" name="email" required placeholder="your@email.com"
                            class="glass-input w-full">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="glass-label">Phone Number</label>
                        <input type="tel" name="phone" required placeholder="+91 XXXXX XXXXX"
                            class="glass-input w-full">
                    </div>
                    <div>
                        <label class="glass-label">Travel Date</label>
                        <input type="date" name="date" required class="glass-input w-full">
                    </div>
                </div>

                <div>
                    <label class="glass-label">Special Requests (Optional)</label>
                    <textarea name="requests" rows="3" placeholder="Any special requirements or preferences?"
                        class="glass-textarea w-full"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="glass-button w-full">
                        Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>