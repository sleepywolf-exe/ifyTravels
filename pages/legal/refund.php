<?php
// pages/legal/refund.php
require_once '../../includes/header.php';
?>

<main class="min-h-screen bg-slate-50 pt-32 pb-24 relative overflow-hidden">
    <!-- Background Decor -->
    <div
        class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-red-500/5 rounded-full blur-3xl mix-blend-multiply opacity-40 pointer-events-none translate-x-1/4 translate-y-1/4">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Header -->
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-slate-900 mb-6 tracking-tight">Refund Policy
            </h1>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto font-light">
                Transparent policies for your peace of mind.
            </p>
        </div>

        <!-- Content Card -->
        <div
            class="max-w-4xl mx-auto bg-white/80 backdrop-blur-xl rounded-3xl shadow-creative p-8 md:p-16 border border-white/60">
            <div
                class="prose prose-lg prose-slate max-w-none prose-headings:font-heading prose-headings:font-bold prose-headings:text-slate-800 prose-a:text-primary hover:prose-a:text-primary/80">
                <p
                    class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-8 border-b border-slate-100 pb-4">
                    Updated: January 2026</p>

                <h3>1. Cancellation Policy</h3>
                <ul class="marker:text-primary">
                    <li><strong>30+ Days Prior:</strong> Cancellations made 30 days or more prior to the departure date
                        will receive a 100% refund, minus a $50 processing fee.</li>
                    <li><strong>15-29 Days Prior:</strong> Cancellations made between 15 and 29 days prior to departure
                        will receive a 50% refund.</li>
                    <li><strong>0-14 Days Prior:</strong> Cancellations made within 14 days of departure are <span
                            class="text-red-500 font-semibold">non-refundable</span>.</li>
                </ul>

                <h3>2. Refund Process</h3>
                <p>Refunds will be processed back to the original payment method within <strong>7-10 business
                        days</strong> after the cancellation request is approved. You will receive an email confirmation
                    once the refund is initiated.</p>

                <h3>3. Flight Tickets</h3>
                <p>Flight tickets booked through ifyTravels are subject to the specific airline's cancellation policy.
                    Often, promotional fares are non-refundable. Please check the ticket conditions at the time of
                    booking.</p>

                <h3>4. Force Majeure</h3>
                <p>In the event of cancellation due to force majeure (natural disasters, pandemics, government
                    restrictions, etc.), ifyTravels will work tirelessly with suppliers (hotels, airlines) to secure
                    refunds or future travel credits for you. However, we cannot guarantee full reimbursement if the
                    supplier denies it.</p>

                <div class="mt-12 p-6 bg-blue-50/50 rounded-2xl border border-blue-100">
                    <h4 class="text-lg font-bold text-blue-900 mb-2 mt-0">Need to cancel?</h4>
                    <p class="mb-0 text-blue-700/80">Visit your <a href="<?php echo base_url('user/dashboard.php'); ?>"
                            class="text-blue-700 font-semibold underline decoration-2 decoration-blue-300 hover:decoration-blue-700">Dashboard</a>
                        or contact our support team immediately.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>