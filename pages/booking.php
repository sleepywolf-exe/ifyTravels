<?php
$pageTitle = isset($package) ? "Book: " . $package['title'] : "Plan Your Journey";
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

// Facebook CAPI: InitiateCheckout
if (isset($fbCapi)) {
    $fbCapi->sendEvent('InitiateCheckout', [
        'content_category' => 'Package',
        'content_ids' => [$selectedPkg['id']],
        'content_name' => $selectedPkg['title'],
        'num_items' => 1,
        'value' => $selectedPkg['price'],
        'currency' => 'INR'
    ], $userData);
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Booking Page Content -->
<div class="relative min-h-screen bg-slate-50 pt-40 pb-20">

    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo base_url($selectedPkg['image']); ?>"
            class="w-full h-full object-cover opacity-[0.15] blur-[2px] fixed inset-0" alt="Background">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-50/90 via-slate-50/80 to-slate-50"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">

        <!-- Page Header (Full Width) -->
        <div class="max-w-6xl mx-auto mb-16 pt-10">
            <span
                class="inline-block py-1 px-3 rounded-full bg-indigo-50 text-indigo-600 font-bold tracking-widest uppercase text-[10px] mb-6 border border-indigo-100">
                Secure Your Trip
            </span>
            <h1
                class="text-4xl md:text-8xl lg:text-9xl font-heading font-black text-slate-900 leading-[0.9] tracking-tighter mb-6">
                Complete <br>
                <span class="font-serif italic text-primary pr-2">Booking</span>
            </h1>
            <div class="w-24 h-2 bg-primary mb-8 rounded-full"></div>
            <p class="text-slate-500 font-medium leading-relaxed text-lg max-w-xl">
                You're just one step away from your dream vacation.
            </p>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

            <!-- Package Summary Panel (Left - Sticky) -->
            <div class="lg:col-span-1 sticky top-32 space-y-6">

                <!-- Glassmorphic Package Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-3xl overflow-hidden shadow-xl ring-1 ring-slate-900/5 group">
                    <div class="relative h-64">
                        <img src="<?php echo base_url($selectedPkg['image']); ?>"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            alt="<?php echo htmlspecialchars($selectedPkg['title']); ?>">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-90">
                        </div>
                        <div class="absolute bottom-5 left-5 right-5 z-10">
                            <h3 class="text-xl font-heading font-bold text-white leading-tight drop-shadow-sm mb-2">
                                <?php echo htmlspecialchars($selectedPkg['title']); ?>
                            </h3>
                            <div
                                class="flex items-center text-xs text-slate-300 font-bold uppercase tracking-wider gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo $selectedPkg['duration']; ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-end border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total
                                    Base Price</p>
                                <p class="text-3xl font-black text-slate-800 font-heading">
                                    ₹<?php echo number_format($selectedPkg['price']); ?>
                                </p>
                            </div>
                            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded">per
                                person</span>
                        </div>
                    </div>
                </div>

                <!-- Help Box Redesigned -->
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden group border border-slate-700/50">
                    <!-- Decorative Elements -->
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-primary/20 rounded-full blur-3xl -mr-10 -mt-10 transition-transform duration-700 group-hover:scale-150">
                    </div>

                    <h4 class="font-heading font-black text-xl text-white mb-3 relative z-10">
                        Need Assistance?
                    </h4>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed relative z-10 font-medium">
                        Our experts are available 24/7 to help you.
                    </p>

                    <a href="tel:+919999779870"
                        class="flex items-center justify-between bg-white/10 backdrop-blur-md hover:bg-white/20 text-white rounded-xl px-4 py-3 transition-all duration-300 border border-white/10 group-hover:border-primary/50 relative z-10">
                        <span class="flex items-center gap-3 font-bold text-sm">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            +91 9999-779-870
                        </span>
                    </a>
                </div>

            </div>

            <!-- Booking Form (Right) -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white border border-slate-100 rounded-[2.5rem] shadow-xl shadow-slate-200/40 p-8 md:p-12 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none">
                    </div>

                    <form id="booking-form" method="POST" class="space-y-8">
                        <?php if (class_exists('SpamProtection'))
                            echo SpamProtection::generateFields(); ?>
                        <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($pkgId); ?>">
                        <input type="hidden" name="package_name"
                            value="<?php echo htmlspecialchars($selectedPkg['title']); ?>">

                        <!-- Personal Details -->
                        <div>
                            <h3 class="text-2xl font-heading font-bold text-slate-900 mb-6 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-slate-900 text-white text-sm font-bold flex items-center justify-center shadow-lg">1</span>
                                Personal Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Full Name</label>
                                    <input type="text" name="customer_name" required placeholder="Ex: John Doe"
                                        class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all placeholder-slate-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Email Address</label>
                                    <input type="email" name="email" required placeholder="Ex: john@example.com"
                                        class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all placeholder-slate-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Phone Number</label>
                                    <input type="tel" name="phone" required placeholder="Ex: +91 98765 43210"
                                        class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all placeholder-slate-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Travel Date</label>
                                    <input type="date" name="travel_date" required min="<?php echo date('Y-m-d'); ?>"
                                        class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all placeholder-slate-400 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Traveler Details -->
                        <div>
                            <h3 class="text-2xl font-heading font-bold text-slate-900 mb-6 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-slate-900 text-white text-sm font-bold flex items-center justify-center shadow-lg">2</span>
                                Traveler Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Adults (12+ yrs)</label>
                                    <div class="relative">
                                        <input type="number" name="adults" id="adults" min="1"
                                            value="<?php echo max(1, $presetTravelers); ?>"
                                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold text-lg focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Children (2-12 yrs)</label>
                                    <div class="relative">
                                        <input type="number" name="children" id="children" min="0" value="0"
                                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold text-lg focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                    </div>
                                </div>
                            </div>

                            <!-- Customization Toggle -->
                            <div
                                class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex items-center justify-between group hover:border-indigo-200 transition-colors">
                                <div>
                                    <span class="text-indigo-900 font-bold block mb-1">Customize this package?</span>
                                    <span class="text-indigo-600/80 text-xs block font-medium">Get a tailored itinerary
                                        directly from our experts.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="customizeToggle" name="is_customized" value="1"
                                        class="sr-only peer">
                                    <div
                                        class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-inner">
                                    </div>
                                </label>
                            </div>

                            <!-- Custom Fields -->
                            <div id="customFields"
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden section-fade-in">
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Preferred Duration</label>
                                    <input type="text" name="duration"
                                        value="<?php echo htmlspecialchars($selectedPkg['duration']); ?>"
                                        class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-slate-700 text-sm font-bold ml-1">Hotel Category</label>
                                    <div class="relative">
                                        <select name="hotel_category"
                                            class="w-full px-5 py-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none">
                                            <option value="Budget">Budget</option>
                                            <option value="Mid-range" selected>Mid-range</option>
                                            <option value="Luxury">Luxury</option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- Payment & Submit -->
                        <div>
                            <div
                                class="flex justify-between items-center bg-slate-900 p-6 rounded-2xl shadow-xl mb-8 relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16">
                                </div>
                                <span class="text-white/80 font-bold relative z-10">Estimated Total</span>
                                <span id="pkg-price"
                                    class="text-4xl font-heading font-black text-white relative z-10 tracking-tight">
                                    ₹<?php echo number_format($selectedPkg['price']); ?>
                                </span>
                            </div>

                            <input type="hidden" name="total_price" id="hiddenTotalPrice"
                                value="<?php echo $selectedPkg['price'] * $presetTravelers; ?>">

                            <button type="submit" id="submitBtn"
                                class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-lg py-5 rounded-xl shadow-xl shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98] magnetic-btn relative overflow-hidden group">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    Confirm & Book Now
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </span>
                            </button>

                            <p class="text-center text-slate-400 text-xs mt-6 flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                SSL Secured Payment. By booking, you agree to our Terms.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('booking-form');
        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const totalPriceEl = document.getElementById('pkg-price');
        const hiddenTotalPrice = document.getElementById('hiddenTotalPrice');
        const customizeToggle = document.getElementById('customizeToggle');
        const customFields = document.getElementById('customFields');
        const submitBtn = document.getElementById('submitBtn');
        const basePrice = <?php echo $selectedPkg ? $selectedPkg['price'] : 0; ?>;

        function updatePrice() {
            if (customizeToggle.checked) {
                totalPriceEl.textContent = "On Request";
                hiddenTotalPrice.value = 0;
                customFields.classList.remove('hidden');
                submitBtn.textContent = "Request Custom Quote";
            } else {
                const adults = parseInt(adultsInput.value) || 0;
                const children = parseInt(childrenInput.value) || 0;
                const total = (adults + children) * basePrice;

                totalPriceEl.textContent = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(total);
                hiddenTotalPrice.value = total;
                customFields.classList.add('hidden');
                submitBtn.textContent = "Confirm & Book Now";
            }
        }

        adultsInput.addEventListener('input', updatePrice);
        childrenInput.addEventListener('input', updatePrice);
        customizeToggle.addEventListener('change', updatePrice);
        updatePrice();

        // Form Submit
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const originalText = submitBtn.textContent;
            submitBtn.textContent = "Processing...";
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const response = await fetch('<?php echo base_url('services/submit_booking.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.status === 'success') {
                    window.location.href = '<?php echo base_url('booking-success?id='); ?>' + result.booking_id;
                } else {
                    throw new Error(result.message || "Booking failed.");
                }
            } catch (err) {
                alert(err.message);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>