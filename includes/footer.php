<?php
// Footer Component
?>
<footer class="bg-gradient-to-b from-slate-50 to-white text-slate-800 pt-20 pb-10 mt-auto relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl mix-blend-multiply"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl mix-blend-multiply"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <!-- Brand Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <a href="<?php echo base_url('index.php'); ?>"
                    class="text-2xl font-bold text-slate-900 mb-4 block flex items-center">
                    <img src="<?php echo base_url('assets/images/logo-color.png?v=' . time()); ?>"
                        alt="ifyTravels Footer Logo" width="150" height="40" loading="lazy" class="h-12 object-contain">
                </a>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">
                    <?php echo e(get_setting('footer_description', 'Curating unforgettable journeys...')); ?>
                </p>
                <div class="flex space-x-3">
                    <a href="<?php echo e(get_setting('social_facebook', '#')); ?>" target="_blank" rel="noopener"
                        class="group w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 hover:text-white hover:bg-blue-600 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_twitter', '#')); ?>" target="_blank" rel="noopener"
                        class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 hover:text-white hover:bg-sky-500 transition-all duration-300 hover:shadow-lg hover:shadow-sky-400/30 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_instagram', '#')); ?>" target="_blank" rel="noopener"
                        class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 hover:text-white hover:bg-pink-600 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/30 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Explore Section -->
            <div class="transform hover:scale-105 transition duration-300">
                <h3 class="font-bold text-lg mb-4 text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Explore
                </h3>
                <ul class="space-y-3 text-gray-500 text-sm">
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
                <h3 class="font-bold text-lg mb-4 text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Legal & Support
                </h3>
                <ul class="space-y-3 text-gray-500 text-sm">
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
                <h3 class="font-bold text-lg mb-4 text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Secure Payments
                </h3>
                <p class="text-gray-500 text-sm mb-4">
                    <?php echo e(get_setting('footer_payment_text', 'We accept all major payment methods')); ?>
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <!-- Visa Card -->
                    <div
                        class="bg-white border boundary-gray-200 rounded px-2 py-1 flex items-center justify-center hover:opacity-90 transition h-8 shadow-sm">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa"
                            width="40" height="25" loading="lazy">
                    </div>
                    <!-- Mastercard -->
                    <div
                        class="bg-white border boundary-gray-200 rounded px-2 py-1 flex items-center justify-center hover:opacity-90 transition h-8 shadow-sm">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                            alt="Mastercard" width="40" height="25" loading="lazy">
                    </div>
                    <!-- UPI -->
                    <div
                        class="bg-white border boundary-gray-200 rounded px-2 py-1 flex items-center justify-center hover:opacity-90 transition h-8 shadow-sm">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg" alt="UPI"
                            width="40" height="25" loading="lazy">
                    </div>
                    <!-- Rupay -->
                    <div
                        class="bg-green-50/50 border border-green-100 backdrop-blur-sm rounded-lg p-2 hover:bg-green-100 transition flex items-center justify-center col-span-3">
                        <div class="flex items-center space-x-2 text-green-700 text-xs font-bold">
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
        <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
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


</body>

</html>