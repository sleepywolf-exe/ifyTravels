<?php
// Footer Component
?>
<footer
    class="bg-slate-50 text-slate-800 pt-48 pb-24 mt-auto relative overflow-hidden border-t border-slate-200 hidden md:block">
    <!-- Massive Background Text -->
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
        <h2
            class="text-[20rem] md:text-[35rem] font-black text-slate-900 opacity-[0.03] leading-none tracking-tighter uppercase font-heading transform translate-y-1/4">
            World
        </h2>
    </div>

    <!-- Decorative Elements (Subtle) -->
    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-3xl mix-blend-multiply opacity-50 pointer-events-none">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16 mb-24">
            <!-- Brand Section (Big) -->
            <div class="col-span-1 md:col-span-4 transform transition duration-300 reveal-footer-col">
                <a href="<?php echo base_url(''); ?>" class="block mb-8">
                    <img src="<?php echo base_url('assets/images/logo-color.png?v=' . time()); ?>"
                        alt="ifyTravels Footer Logo" width="200" height="60" loading="lazy" class="h-20 object-contain">
                </a>
                <p class="text-slate-500 text-xl leading-relaxed mb-10 max-w-sm font-light">
                    <?php echo e(get_setting('footer_description', 'Curating unforgettable journeys for the modern explorer. Experience the world with premium packages and expert guidance.')); ?>
                </p>

                <!-- Large Social Icons -->
                <div class="flex space-x-4">
                    <a href="<?php echo e(get_setting('social_facebook', '#')); ?>" target="_blank" rel="noopener"
                        class="w-14 h-14 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_twitter', '#')); ?>" target="_blank" rel="noopener"
                        class="w-14 h-14 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-slate-400 hover:text-white hover:bg-sky-500 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="<?php echo e(get_setting('social_instagram', '#')); ?>" target="_blank" rel="noopener"
                        class="w-14 h-14 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-slate-400 hover:text-white hover:bg-pink-600 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Links Sections (Narrower, More Focus) -->
            <div class="col-span-1 md:col-span-2 reveal-footer-col pl-0 md:pl-6" style="transition-delay: 100ms">
                <h3 class="font-heading font-bold text-2xl mb-8 text-slate-900 flex items-center tracking-tight">
                    <span class="w-1.5 h-8 bg-primary/20 rounded-full mr-4 relative"><span
                            class="absolute top-0 left-0 w-full h-1/2 bg-primary rounded-full"></span></span>
                    Explore
                </h3>
                <ul class="space-y-5 text-lg text-slate-500 font-medium">
                    <li><a href="<?php echo base_url('destinations'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Top
                            Destinations</a></li>
                    <li><a href="<?php echo base_url('packages'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Exclusive
                            Packages</a></li>
                    <li><a href="<?php echo base_url('blogs'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Travel
                            Blogs</a></li>
                    <li><a href="<?php echo base_url('contact'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Contact
                            Us</a></li>
                </ul>
            </div>

            <div class="col-span-1 md:col-span-2 reveal-footer-col" style="transition-delay: 200ms">
                <h3 class="font-heading font-bold text-2xl mb-8 text-slate-900 flex items-center tracking-tight">
                    <span class="w-1.5 h-8 bg-secondary/20 rounded-full mr-4 relative"><span
                            class="absolute bottom-0 left-0 w-full h-1/2 bg-secondary rounded-full"></span></span>
                    Legal
                </h3>
                <ul class="space-y-5 text-lg text-slate-500 font-medium">
                    <li><a href="<?php echo base_url('pages/legal/terms.html'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Terms
                            of Use</a></li>
                    <li><a href="<?php echo base_url('pages/legal/privacy.html'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Privacy
                            Policy</a></li>
                    <li><a href="<?php echo base_url('pages/legal/refund.html'); ?>"
                            class="hover:text-primary transition-all duration-300 hover:pl-2 flex items-center group"><span
                                class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-primary transition-colors mr-3"></span>Refund
                            Policy</a></li>
                </ul>
            </div>

            <!-- Payment Section (Massive Space) -->
            <div class="col-span-1 md:col-span-4 reveal-footer-col flex flex-col justify-between h-full pl-0 md:pl-12 border-l border-slate-100"
                style="transition-delay: 300ms">
                <div>
                    <h3 class="font-heading font-black text-3xl mb-6 text-slate-900 tracking-tight">
                        Secure Payments
                    </h3>
                    <p class="text-slate-500 text-xl mb-10 leading-relaxed font-light max-w-sm">
                        Experience peace of mind with our bank-grade encrypted payment gateway.
                    </p>
                    <div class="flex items-center gap-10 mb-10">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg"
                            class="h-10 w-auto grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 hover:scale-110 cursor-pointer"
                            alt="Visa" title="Verified by Visa">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                            class="h-12 w-auto grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 hover:scale-110 cursor-pointer"
                            alt="Mastercard" title="Mastercard SecureCode">

                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg"
                            class="h-10 w-auto grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 hover:scale-110 cursor-pointer"
                            alt="UPI" title="UPI Payments">
                    </div>
                </div>

                <div class="inline-flex items-center gap-4 group cursor-pointer w-full">
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-100 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs uppercase tracking-[0.2em] text-emerald-600 font-bold mb-1">Official
                            Partner</span>
                        <span class="block text-slate-900 font-bold text-lg">100% Verified & Secure</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div
            class="border-t border-slate-200 pt-10 flex flex-col md:flex-row justify-between items-center text-slate-400">
            <p class="text-base mb-4 md:mb-0 font-medium">
                <?php echo e(get_setting('footer_copyright', '&copy; ' . current_year() . ' ifyTravels. All rights reserved.')); ?>
            </p>
            <div class="flex items-center space-x-8 text-sm font-semibold tracking-wide uppercase">
                <span class="flex items-center hover:text-primary transition-colors cursor-pointer"><span
                        class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>System Operational</span>
                <span class="flex items-center hover:text-primary transition-colors cursor-pointer"><span
                        class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-2"></span>v4.0.0 (Beta)</span>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Footer (Copyright Only) -->
<div class="md:hidden py-8 px-6 text-center text-slate-400 border-t border-slate-100 bg-slate-50 mb-20">
    <p class="text-xs">&copy; <?php echo current_year(); ?> ifyTravels. All rights reserved.</p>
    <div class="flex justify-center gap-4 mt-2 text-xs">
        <a href="<?php echo base_url('pages/legal/terms.html'); ?>" class="hover:underline">Terms</a>
        <a href="<?php echo base_url('pages/legal/privacy.html'); ?>" class="hover:underline">Privacy</a>
    </div>
</div>

<?php
// Include Mobile Bottom Nav (unless hidden)
if (!isset($hideMobileNav) || !$hideMobileNav) {
    include __DIR__ . '/mobile_nav.php';
}
?>

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