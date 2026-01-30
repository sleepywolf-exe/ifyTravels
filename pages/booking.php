<?php
$pageTitle = isset($package) ? "Book: " . $package['title'] : "Plan Your Code Trip";
include __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../data/loader.php';

$pkgId = $_GET['packageId'] ?? null;
$selectedPkg = null;

if ($pkgId) {
    $selectedPkg = getPackageById($pkgId);
}

// Enforce Package Selection
if (!$pkgId || !$selectedPkg) {
    header('Location: ' . base_url('packages'));
    exit;
}

$presetTravelers = $_GET['travelers'] ?? 1;

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
                         alt="Selected Package" 
                         width="300" height="200" loading="lazy"
                         class="w-full h-full object-cover rounded-lg mb-4 shadow-sm">
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
            <?php endif; ?>
        </div>

        <!-- Form -->
        <div class="md:w-2/3 p-8 glass-form" style="background: linear-gradient(135deg, #0F766E 0%, #0d9488 100%);">
            <!-- Booking Form V2 -->

            <form id="booking-form" method="POST" class="space-y-6">
                <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($pkgId); ?>">
                <input type="hidden" name="package_name"
                    value="<?php echo $selectedPkg ? htmlspecialchars($selectedPkg['title']) : 'Custom Inquiry'; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="glass-label">Full Name</label>
                        <input type="text" name="customer_name" required placeholder="Enter your full name"
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
                        <input type="date" name="travel_date" required min="<?php echo date('Y-m-d'); ?>"
                            class="glass-input w-full">
                    </div>
                </div>

                <!-- Customization Toggle -->
                <div class="mb-6 bg-white/10 p-4 rounded-xl border border-white/20">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="customizeToggle" name="is_customized" value="1" class="sr-only peer">
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-white">I want to customize this package</span>
                    </label>
                    <p class="text-xs text-teal-100 mt-2 hidden" id="customMsg">
                        Note: For customized packages, the price will be calculated by our experts based on your
                        requirements.
                    </p>
                </div>

                <!-- Custom Fields (Hidden by default) -->
                <div id="customFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 hidden">
                    <!-- Duration (Days) -->
                    <div>
                        <label class="block text-white text-sm font-bold mb-2">Duration (Days)</label>
                        <input type="text" name="duration" id="duration"
                            value="<?php echo htmlspecialchars($selectedPkg['duration']); ?>"
                            class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-300 backrop-blur-sm"
                            placeholder="e.g. 5 Days">
                    </div>

                    <!-- Hotel Category -->
                    <div>
                        <label class="block text-white text-sm font-bold mb-2">Hotel Category</label>
                        <select name="hotel_category"
                            class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white focus:outline-none focus:ring-2 focus:ring-teal-300 backrop-blur-sm [&>option]:text-gray-900">
                            <option value="Budget">Budget</option>
                            <option value="Mid-range" selected>Mid-range</option>
                            <option value="Luxury">Luxury</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Adults -->
                    <div>
                        <label class="block text-white text-sm font-bold mb-2">Adults (12+ yrs)</label>
                        <div class="relative">
                            <input type="number" name="adults" id="adults" min="1"
                                value="<?php echo max(1, $presetTravelers); ?>"
                                class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-300 backrop-blur-sm pl-10"
                                required>
                            <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Children -->
                    <div>
                        <label class="block text-white text-sm font-bold mb-2">Children (2-12 yrs)</label>
                        <div class="relative">
                            <input type="number" name="children" id="children" min="0" value="0"
                                class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-300 backrop-blur-sm pl-10">
                            <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Special Requests -->
                <div class="mb-6">
                    <label class="block text-white text-sm font-bold mb-2">Special Requests (Optional)</label>
                    <textarea name="message"
                        class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-300 backrop-blur-sm h-24"
                        placeholder="Any special requirements or preferences?"></textarea>
                </div>

                <input type="hidden" name="total_price" id="hiddenTotalPrice"
                    value="<?php echo $selectedPkg['price'] * $presetTravelers; ?>">

                <button type="submit" id="submitBtn"
                    class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white font-bold py-4 rounded-xl shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                    Send Request
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const form = document.getElementById('booking-form');
        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const totalPriceEl = document.getElementById('pkg-price');
        const hiddenTotalPrice = document.getElementById('hiddenTotalPrice');
        const customizeToggle = document.getElementById('customizeToggle');
        const customMsg = document.getElementById('customMsg');
        const customFields = document.getElementById('customFields');
        const submitBtn = document.getElementById('submitBtn');
        const dateInput = document.querySelector('input[name="travel_date"]');
        const phoneInput = document.querySelector('input[name="phone"]');

        // 1. Price Calculation
        const basePrice = <?php echo $selectedPkg ? $selectedPkg['price'] : 0; ?>;

        function updatePrice() {
            if (customizeToggle.checked) {
                // Customize Mode
                if (totalPriceEl) {
                    totalPriceEl.textContent = "Price on Request";
                    totalPriceEl.classList.add('text-lg');
                }
                hiddenTotalPrice.value = 0;

                customMsg.classList.remove('hidden');
                customFields.classList.remove('hidden');
                submitBtn.textContent = "Request Custom Package";
            } else {
                // Standard Mode
                const adults = parseInt(adultsInput.value) || 0;
                const children = parseInt(childrenInput.value) || 0;
                const totalPax = adults + children;

                const total = totalPax * basePrice;

                const formatted = new Intl.NumberFormat('en-IN', {
                    style: 'currency',
                    currency: 'INR',
                    maximumFractionDigits: 0
                }).format(total);

                if (totalPriceEl) {
                    totalPriceEl.textContent = formatted;
                    totalPriceEl.classList.remove('text-lg');
                }
                hiddenTotalPrice.value = total;

                customMsg.classList.add('hidden');
                customFields.classList.add('hidden');
                submitBtn.textContent = "Send Request";
            }
        }

        adultsInput.addEventListener('input', updatePrice);
        childrenInput.addEventListener('input', updatePrice);
        customizeToggle.addEventListener('change', updatePrice);

        // Initial Run
        updatePrice();

        // 2. Form Submission (AJAX via Secure API)
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous errors
            [dateInput, phoneInput].forEach(el => el.classList.remove('border-red-500', 'ring-2', 'ring-red-500'));

            // Client-side Validation (Basic)
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                alert("Travel date cannot be in the past.");
                dateInput.classList.add('border-red-500', 'ring-2', 'ring-red-500');
                return;
            }

            const phone = phoneInput.value.replace(/\D/g, '');
            if (phone.length < 10) {
                alert("Please enter a valid phone number.");
                phoneInput.classList.add('border-red-500', 'ring-2', 'ring-red-500');
                return;
            }

            // Prepare Data
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            // Add array fields (interests) manually if needed or ensure name="interests[]" works with specific logic if applicable
            // For simple implementation, handle checkbox arrays:
            const interests = Array.from(document.querySelectorAll('input[name="interests[]"]:checked')).map(el => el.value);
            if (interests.length > 0) data['interests'] = interests;

            // UI Feedback
            const originalBtnText = submitBtn.textContent;
            submitBtn.textContent = "Sending...";
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch('<?php echo base_url('services/submit_booking.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // Success!
                    window.location.href = '<?php echo base_url('booking-success?id='); ?>' + result.booking_id;
                } else {
                    throw new Error(result.message || "Booking failed. Please try again.");
                }

            } catch (err) {
                console.error(err);
                alert(err.message);
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>