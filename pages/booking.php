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
    header('Location: ' . base_url('pages/packages.php'));
    exit;
}

$presetTravelers = $_GET['travelers'] ?? 1;

include __DIR__ . '/../includes/header.php';
?>

<!-- Booking Page Content -->
<div class="relative min-h-screen bg-charcoal flex items-center justify-center py-20">
    
    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo base_url($selectedPkg['image']); ?>" class="w-full h-full object-cover opacity-20 blur-sm" alt="Background">
        <div class="absolute inset-0 bg-gradient-to-b from-charcoal via-charcoal/80 to-charcoal"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Package Summary Panel (Left) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Header -->
                <div>
                    <span class="text-secondary font-bold tracking-widest uppercase text-xs mb-2 block">Secure Your Trip</span>
                    <h1 class="text-4xl font-heading font-bold text-white mb-4">Complete Booking</h1>
                    <p class="text-gray-400 text-sm leading-relaxed">You are just one step away from your dream vacation. Fill in the details below to proceed.</p>
                </div>

                <!-- Package Card -->
                <div class="glass-form !p-0 !bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                    <img src="<?php echo base_url($selectedPkg['image']); ?>" class="w-full h-48 object-cover opacity-80" alt="<?php echo htmlspecialchars($selectedPkg['title']); ?>">
                    <div class="p-6">
                         <h3 class="text-xl font-heading font-bold text-white mb-2"><?php echo htmlspecialchars($selectedPkg['title']); ?></h3>
                         <div class="flex items-center text-sm text-gray-400 mb-4">
                             <svg class="w-4 h-4 mr-2 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <?php echo $selectedPkg['duration']; ?>
                         </div>
                         <div class="border-t border-white/10 pt-4 flex justify-between items-center">
                             <span class="text-gray-400 text-sm">Base Price</span>
                             <span class="text-xl font-bold text-white">₹<?php echo number_format($selectedPkg['price']); ?></span>
                         </div>
                    </div>
                </div>

                <!-- Help Box -->
                <div class="bg-gradient-to-br from-secondary/20 to-secondary/5 border border-secondary/20 rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Need Assistance?
                    </h4>
                    <p class="text-gray-300 text-sm mb-4">Our travel experts are available 24/7 to help you.</p>
                    <a href="tel:+919999779870" class="text-secondary font-bold text-sm hover:text-white transition">Call +91 9999-779-870</a>
                </div>
            </div>

            <!-- Booking Form (Right) -->
            <div class="lg:col-span-2">
                 <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl">
                     <form id="booking-form" method="POST" class="space-y-6">
                        <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($pkgId); ?>">
                        <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($selectedPkg['title']); ?>">

                        <!-- Personal Details -->
                        <div>
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-secondary text-white text-xs flex items-center justify-center">1</span>
                                Personal Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Full Name</label>
                                    <input type="text" name="customer_name" required placeholder="John Doe" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Email Address</label>
                                    <input type="email" name="email" required placeholder="john@example.com" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Phone Number</label>
                                    <input type="tel" name="phone" required placeholder="+91 98765 43210" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Travel Date</label>
                                    <input type="date" name="travel_date" required min="<?php echo date('Y-m-d'); ?>" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition [color-scheme:dark]">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-white/10 my-6"></div>

                        <!-- Traveler Details -->
                        <div>
                             <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-secondary text-white text-xs flex items-center justify-center">2</span>
                                Traveler Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Adults (12+ yrs)</label>
                                    <input type="number" name="adults" id="adults" min="1" value="<?php echo max(1, $presetTravelers); ?>" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Children (2-12 yrs)</label>
                                    <input type="number" name="children" id="children" min="0" value="0" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                            </div>

                            <!-- Customization Toggle -->
                            <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                                <div>
                                    <span class="text-white font-bold block text-sm">Customize this package?</span>
                                    <span class="text-gray-400 text-xs block">Prices will be calculated by our experts for custom plans.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="customizeToggle" name="is_customized" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                                </label>
                            </div>

                            <!-- Custom Fields -->
                            <div id="customFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Preferred Duration</label>
                                    <input type="text" name="duration" value="<?php echo htmlspecialchars($selectedPkg['duration']); ?>" class="glass-input w-full !bg-white/5 !border-white/10 text-white">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Hotel Category</label>
                                    <select name="hotel_category" class="glass-input w-full !bg-white/5 !border-white/10 text-white [&>option]:text-charcoal">
                                        <option value="Budget">Budget</option>
                                        <option value="Mid-range" selected>Mid-range</option>
                                        <option value="Luxury">Luxury</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-white/10 my-6"></div>

                        <!-- Payment & Submit -->
                        <div>
                            <div class="flex justify-between items-center bg-secondary/10 p-4 rounded-xl border border-secondary/20 mb-6">
                                <span class="text-white font-bold">Estimated Total</span>
                                <span id="pkg-price" class="text-2xl font-bold text-secondary">₹<?php echo number_format($selectedPkg['price']); ?></span>
                            </div>

                            <input type="hidden" name="total_price" id="hiddenTotalPrice" value="<?php echo $selectedPkg['price'] * $presetTravelers; ?>">

                            <button type="submit" id="submitBtn" class="w-full bg-secondary hover:bg-yellow-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 transform hover:-translate-y-1">
                                Confirm & Book Now
                            </button>
                            <p class="text-center text-gray-500 text-xs mt-4">By booking, you agree to our Terms & Conditions and Privacy Policy.</p>
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
                    window.location.href = '<?php echo base_url('pages/booking-success.php?id='); ?>' + result.booking_id;
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