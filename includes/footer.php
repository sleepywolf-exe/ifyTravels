<?php
// Footer Component
?>
<footer
    class="bg-gradient-to-br from-charcoal via-gray-900 to-charcoal text-white pt-20 pb-10 mt-auto relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-500/5 rounded-full blur-3xl"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <!-- Brand Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <a href="<?php echo base_url('index.php'); ?>"
                    class="text-2xl font-bold text-white mb-4 block flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <?php echo e(get_setting('site_name', 'ifyTravels')); ?>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    <?php echo e(get_setting('footer_description', 'Curating unforgettable journeys...')); ?>
                </p>
                <div class="flex space-x-3">
                    <a href="<?php echo e(get_setting('social_facebook', '#')); ?>" target="_blank" rel="noopener"
                        class="group w-10 h-10 rounded-full bg-gray-800/80 flex items-center justify-center hover:bg-gradient-to-r hover:from-blue-600 hover:to-blue-500 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/50 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_twitter', '#')); ?>" target="_blank" rel="noopener"
                        class="w-10 h-10 rounded-full bg-gray-800/80 flex items-center justify-center hover:bg-gradient-to-r hover:from-blue-400 hover:to-blue-600 transition-all duration-300 hover:shadow-lg hover:shadow-blue-400/50 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_instagram', '#')); ?>" target="_blank" rel="noopener"
                        class="w-10 h-10 rounded-full bg-gray-800/80 flex items-center justify-center hover:bg-gradient-to-r hover:from-pink-600 hover:via-purple-600 hover:to-orange-500 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/50 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Explore Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Explore
                </h3>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="<?php echo base_url('pages/destinations.php'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Top Destinations
                        </a></li>
                    <li><a href="<?php echo base_url('pages/packages.php'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Exclusive Packages
                        </a></li>
                    <li><a href="<?php echo base_url('pages/contact.php'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Contact Us
                        </a></li>
                </ul>
            </div>

            <!-- Legal Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Legal & Support
                </h3>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="<?php echo base_url('pages/legal/terms.html'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Terms of Use
                        </a></li>
                    <li><a href="<?php echo base_url('pages/legal/privacy.html'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Privacy Policy
                        </a></li>
                    <li><a href="<?php echo base_url('pages/legal/refund.html'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Refund Policy
                        </a></li>
                    <li><a href="<?php echo base_url('pages/contact.php'); ?>"
                            class="hover:text-primary transition flex items-center group">
                            <span
                                class="w-0 group-hover:w-2 h-px bg-primary transition-all duration-300 mr-0 group-hover:mr-2"></span>
                            Help Center
                        </a></li>
                </ul>
            </div>

            <!-- Payment Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <h3 class="font-bold text-lg mb-4 text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Secure Payments
                </h3>
                <p class="text-gray-400 text-sm mb-4">
                    <?php echo e(get_setting('footer_payment_text', 'We accept all major payment methods')); ?>
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <!-- Visa Card -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition flex items-center justify-center group">
                        <svg class="h-6" viewBox="0 0 32 32" fill="none">
                            <rect width="32" height="32" rx="4" fill="white" />
                            <path
                                d="M13.743 19.964h-2.185l-1.36-8.484h2.253l0.874 6.075c0.231 1.625 0.252 1.766 0.252 1.766s0.05-0.252 0.292-1.785l1.643-6.056h2.29l-4.06 8.484zM24.796 11.48h-1.688c-0.52 0-0.916 0.151-1.144 0.695l-4.008 9.537h2.296l0.89-2.474h2.724l0.256 1.256c0.046 0.22 0.22 0.368 0.443 0.368h2.02l-1.79-9.382zM21.572 17.514l1.41-4.043 0.812 4.043h-2.222zM28.082 11.48l-2.02 9.382 0.398-0.082c3.558-0.785 4.332-3.824 4.34-3.856l0.024-0.12-2.742-5.324z"
                                fill="#1434CB" />
                        </svg>
                    </div>
                    <!-- Mastercard -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition flex items-center justify-center">
                        <svg class="h-6" viewBox="0 0 48 32" fill="none">
                            <rect width="48" height="32" rx="4" fill="white" />
                            <circle cx="18" cy="16" r="8" fill="#EB001B" />
                            <circle cx="30" cy="16" r="8" fill="#F79E1B" />
                            <path
                                d="M24 10.5c-1.5 1.3-2.5 3.2-2.5 5.5s1 4.2 2.5 5.5c1.5-1.3 2.5-3.2 2.5-5.5s-1-4.2-2.5-5.5z"
                                fill="#FF5F00" />
                        </svg>
                    </div>
                    <!-- UPI -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition flex items-center justify-center">
                        <svg class="h-6" viewBox="0 0 48 32">
                            <rect width="48" height="32" rx="4" fill="#097939" />
                            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white"
                                font-family="Arial, sans-serif" font-size="10" font-weight="bold">UPI</text>
                        </svg>
                    </div>
                    <!-- Rupay -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition flex items-center justify-center col-span-3">
                        <div class="flex items-center space-x-2 text-white text-xs font-bold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>100% Secure Payments (₹ INR)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800/50 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-500 text-xs mb-4 md:mb-0">
                <?php echo e(get_setting('footer_copyright', '&copy; ' . current_year() . ' ifyTravels. All rights reserved.')); ?>
            </p>
            <div class="flex items-center space-x-6 text-gray-500 text-xs">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    SSL Secured
                </div>
                <span>•</span>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    24/7 Support
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Flatpickr JavaScript Library -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize Flatpickr on homepage travel date input
    document.addEventListener('DOMContentLoaded', function () {
        const travelDateInput = document.getElementById('travel-date');

        if (travelDateInput) {
            flatpickr(travelDateInput, {
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                monthSelectorType: "static",
                disableMobile: false,
                animate: true,
                theme: "material_blue",
                locale: {
                    firstDayOfWeek: 1
                },
                onChange: function (selectedDates, dateStr, instance) {
                    // Optional: add custom behavior when date changes
                    console.log('Date selected:', dateStr);
                }
            });
        }
    });
</script>


<!-- Global Lead/Booking Modal -->
<div id="bookingModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="modalPanel"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div
                    class="bg-gradient-to-r from-primary to-teal-600 px-4 py-4 sm:px-6 flex justify-between items-center">
                    <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">Inquiry</h3>
                    <button type="button" onclick="closeBookingModal()"
                        class="text-white/80 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-4 py-6 sm:p-6">
                    <form id="bookingForm" class="space-y-4">
                        <input type="hidden" name="package_id" id="modalPackageId" value="">
                        <input type="hidden" name="subject" id="modalSubject" value="General Inquiry">

                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="customer_name" id="customer_name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-2 border">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input type="email" name="email" id="email" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-2 border">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="tel" name="phone" id="phone" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-2 border">
                            </div>
                        </div>

                        <div>
                            <label for="travel_date" class="block text-sm font-medium text-gray-700">Travel Date
                                (Tentative)</label>
                            <input type="text" name="travel_date" id="modal_travel_date" placeholder="Select Date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-2 border bg-white">
                        </div>

                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700">Message /
                                Request</label>
                            <textarea name="special_requests" id="special_requests" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-2 border"></textarea>
                        </div>

                        <div id="formFeedback" class="hidden rounded-md p-4 text-sm"></div>

                        <div class="mt-5 sm:mt-6">
                            <button type="submit" id="submitBtn"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-primary px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 sm:text-sm transition-colors">
                                Confirm Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Modal Datepicker
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#modal_travel_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: "true"
        });
    });

    const modal = document.getElementById('bookingModal');
    const backdrop = document.getElementById('modalBackdrop');
    const panel = document.getElementById('modalPanel');
    const form = document.getElementById('bookingForm');
    const feedback = document.getElementById('formFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const modalTitle = document.getElementById('modal-title');
    const modalPackageId = document.getElementById('modalPackageId');
    const modalSubject = document.getElementById('modalSubject');

    // Open Modal Function
    // packageId: ID of package (if booking), null if general inquiry
    // prefillSubject: Title for the inquiry (e.g. "Inquiry for Paris")
    function openLeadModal(packageId = null, prefillSubject = 'General Inquiry') {
        modal.classList.remove('hidden');

        // Reset Logic
        modalPackageId.value = packageId || '';
        modalSubject.value = prefillSubject;

        if (packageId) {
            modalTitle.textContent = 'Book This Package';
            submitBtn.textContent = 'Confirm Booking';
        } else {
            modalTitle.textContent = prefillSubject;
            submitBtn.textContent = 'Send Inquiry';
        }

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeBookingModal() {
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            form.reset();
            feedback.classList.add('hidden');
            submitBtn.disabled = false;
        }, 300);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Sending...';
        feedback.classList.add('hidden');

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Determine Endpoint
            const endpoint = data.package_id ?
                '<?php echo base_url("services/submit_booking.php"); ?>' :
                '<?php echo base_url("services/submit_inquiry.php"); ?>';

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            feedback.classList.remove('hidden');
            if (result.status === 'success') {
                feedback.classList.remove('bg-red-50', 'text-red-800');
                feedback.classList.add('bg-green-50', 'text-green-800');
                feedback.textContent = result.message;
                form.reset();
                setTimeout(closeBookingModal, 2000);
            } else {
                throw new Error(result.message || 'Submission failed');
            }
        } catch (error) {
            feedback.classList.remove('bg-green-50', 'text-green-800');
            feedback.classList.add('bg-red-50', 'text-red-800');
            feedback.textContent = error.message;
        } finally {
            submitBtn.disabled = false;
            if (submitBtn.textContent === 'Sending...') {
                submitBtn.textContent = modalPackageId.value ? 'Confirm Booking' : 'Send Inquiry';
            }
        }
    });
</script>
</body>

</html>