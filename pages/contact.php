<?php
$pageTitle = "Contact Concierge";
include __DIR__ . '/../includes/header.php';
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact ifyTravels",
  "description": "Get in touch with our luxury travel concierge for bespoke inquiries.",
  "url": "<?php echo base_url('pages/contact.php'); ?>"
}
</script>

<?php
$msgSent = false;
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, 'New', ?)");
        $stmt->execute([$name, $email, $subject, $message, date('Y-m-d H:i:s')]);

        send_lead_confirmation_email($email, $name, get_setting('contact_phone', ''));

        $adminData = [
            'Type' => 'General Inquiry',
            'Subject' => $subject,
            'Name' => $name,
            'Email' => $email,
            'Message' => $message
        ];
        send_admin_notification_email("New Inquiry: $name", $adminData, "View Inquiries", base_url("admin/inquiries.php"));

        $msgSent = true;
    } catch (Exception $e) {
        $errorMsg = "An error occurred. Please try again later.";
    }
}
?>

<div class="relative min-h-screen bg-slate-50">

    <!-- Hero Background -->
    <div class="absolute inset-0 h-[60vh] z-0">
        <img src="<?php echo get_setting('contact_bg', base_url('assets/images/destinations/dubai.jpg')); ?>"
            class="w-full h-full object-cover brightness-[0.6] parallax-bg" alt="Contact Background">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-slate-50"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 py-24">

        <!-- Header -->
        <div class="text-center mb-16 animate-fade-in-up pt-10">
            <span class="text-secondary font-bold tracking-widest uppercase text-xs mb-4 block">At Your Service</span>
            <h1 class="text-5xl md:text-7xl font-heading font-bold text-white mb-6 drop-shadow-md">Get in Touch</h1>
            <p class="text-white/90 max-w-2xl mx-auto text-lg font-light leading-relaxed drop-shadow-md">
                Whether you're planning a grand tour or a quick getaway, our concierge team is here to assist you with
                every detail.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 max-w-6xl mx-auto">

            <!-- Contact Information -->
            <div class="lg:w-1/3 space-y-8">

                <!-- Office Card -->
                <div
                    class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl transition group shadow-lg">
                    <div
                        class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Visit Us</h3>
                    <p class="text-slate-500 font-light"><?php echo get_setting('address', '123 Travel Lane, Delhi'); ?>
                    </p>
                </div>

                <!-- Email Card -->
                <div
                    class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl transition group shadow-lg">
                    <div
                        class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Email Concierge</h3>
                    <p class="text-slate-500 font-light">
                        <?php echo get_setting('contact_email', 'hello@ifytravels.com'); ?></p>
                </div>

                <!-- Phone Card -->
                <div
                    class="bg-white border border-slate-100 rounded-3xl p-8 hover:shadow-xl transition group shadow-lg">
                    <div
                        class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Call Anytime</h3>
                    <p class="text-slate-500 font-light"><?php echo get_setting('contact_phone', '+91 987 654 3210'); ?>
                    </p>
                </div>

            </div>

            <!-- Contact Form -->
            <div class="lg:w-2/3">
                <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-10 relative overflow-hidden">

                    <!-- Decorative Background Element -->
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full filter blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none">
                    </div>

                    <?php if ($msgSent): ?>
                        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-heading font-bold text-slate-900 mb-2">Message Received</h3>
                            <p class="text-slate-500 mb-8 max-w-md">Thank you for contacting us. A member of our concierge
                                team will respond shortly.</p>
                            <a href="<?php echo base_url('pages/contact.php'); ?>"
                                class="text-primary font-bold hover:underline text-sm">Send Another Message</a>
                        </div>
                    <?php else: ?>
                        <h2 class="text-3xl font-heading font-bold text-slate-900 mb-8 border-b border-slate-100 pb-6">Send
                            a Message</h2>

                        <?php if ($errorMsg): ?>
                            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100">
                                <?php echo htmlspecialchars($errorMsg); ?>
                            </div>
                        <?php endif; ?>

                        <form id="contact-form" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-slate-600 text-sm mb-2 ml-1 font-medium">Your Name</label>
                                    <input type="text" name="name" required placeholder="John Doe"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                                </div>
                                <div>
                                    <label class="block text-slate-600 text-sm mb-2 ml-1 font-medium">Email Address</label>
                                    <input type="email" name="email" required placeholder="john@example.com"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-600 text-sm mb-2 ml-1 font-medium">Subject</label>
                                <select name="subject"
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                                    <option>General Inquiry</option>
                                    <option>Bespoke Travel Planning</option>
                                    <option>Corporate Partnership</option>
                                    <option>Press & Media</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-slate-600 text-sm mb-2 ml-1 font-medium">How can we help?</label>
                                <textarea name="message" rows="5" required placeholder="Tell us about your travel plans..."
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none placeholder-slate-400"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:to-primary text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1 magnetic-btn">
                                Send Message
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>