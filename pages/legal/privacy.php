<?php
// pages/legal/privacy.php
require_once '../../includes/header.php';
?>

<main class="min-h-screen bg-slate-50 pt-32 pb-24 relative overflow-hidden">
    <!-- Background Decor -->
    <div
        class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl mix-blend-multiply opacity-60 pointer-events-none translate-x-1/2 -translate-y-1/2">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-secondary/5 rounded-full blur-3xl mix-blend-multiply opacity-60 pointer-events-none -translate-x-1/2 translate-y-1/2">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Header -->
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-slate-900 mb-6 tracking-tight">Privacy Policy
            </h1>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto font-light">
                We value your trust. Here is how we collect, use, and protect your data.
            </p>
        </div>

        <!-- Content Card -->
        <div
            class="max-w-4xl mx-auto bg-white/80 backdrop-blur-xl rounded-3xl shadow-creative p-8 md:p-16 border border-white/60">
            <div
                class="prose prose-lg prose-slate max-w-none prose-headings:font-heading prose-headings:font-bold prose-headings:text-slate-800 prose-a:text-primary hover:prose-a:text-primary/80">
                <p
                    class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-8 border-b border-slate-100 pb-4">
                    Last Updated: January 2026</p>

                <h3>1. Data Collection</h3>
                <p>We collect information you provide directly to us, such as when you create an account, update your
                    profile, make a booking, or communicate with us. This ensures we can provide you with a tailored
                    travel experience.</p>

                <h3>2. Use of Information</h3>
                <p>We use the information we collect to operate, maintain, and provide you with the features and
                    functionality of the Service. Your data helps us process transactions, send booking confirmations,
                    and improve our platform.</p>

                <h3>3. GDPR Compliance (EU Users)</h3>
                <p>If you are a resident of the EEU, you have the right to access the Personal Information we hold about
                    you and to ask that your Personal Information be corrected, updated, or deleted. If you would like
                    to exercise this right, please contact us through our support channels.</p>

                <h3>4. Cookies</h3>
                <p>We use cookies to improve your experience. You can choose to disable cookies through your browser
                    settings, but this may affect how certain features of the website function (e.g., keeping you logged
                    in).</p>

                <h3>5. Third-Party Services</h3>
                <p>We may share information with trusted third-party vendors who provide services on our behalf, such as
                    payment processing (Razorpay/Stripe), data analysis, and email delivery. These partners are bound by
                    confidentiality agreements.</p>

                <div class="mt-12 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                    <h4 class="text-lg font-bold text-slate-800 mb-2 mt-0">Questions?</h4>
                    <p class="mb-0 text-slate-600">If you have any questions about this Privacy Policy, please contact
                        us at <a href="mailto:support@ifytravels.com">support@ifytravels.com</a>.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>