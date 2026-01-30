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

<div class="relative min-h-screen bg-charcoal">
    
    <!-- Hero Background -->
    <div class="absolute inset-0">
        <img src="<?php echo get_setting('contact_bg', base_url('assets/images/destinations/dubai.jpg')); ?>" class="w-full h-full object-cover brightness-[0.4]" alt="Contact Background">
        <div class="absolute inset-0 bg-gradient-to-b from-charcoal/50 to-charcoal"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 py-24">
        
        <!-- Header -->
        <div class="text-center mb-16 animate-fade-in-up">
            <span class="text-secondary font-bold tracking-widest uppercase text-xs mb-4 block">At Your Service</span>
            <h1 class="text-5xl md:text-7xl font-heading font-bold text-white mb-6">Get in Touch</h1>
            <p class="text-gray-300 max-w-2xl mx-auto text-lg font-light leading-relaxed">
                Whether you're planning a grand tour or a quick getaway, our concierge team is here to assist you with every detail.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 max-w-6xl mx-auto">

            <!-- Contact Information -->
            <div class="lg:w-1/3 space-y-8">
                
                <!-- Office Card -->
                <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl hover:border-secondary/30 transition group">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-secondary mb-6 group-hover:bg-secondary group-hover:text-white transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-white mb-2">Visit Us</h3>
                    <p class="text-gray-400 font-light"><?php echo get_setting('address', '123 Travel Lane, Delhi'); ?></p>
                </div>

                <!-- Email Card -->
                <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl hover:border-secondary/30 transition group">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-secondary mb-6 group-hover:bg-secondary group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-white mb-2">Email Concierge</h3>
                    <p class="text-gray-400 font-light"><?php echo get_setting('contact_email', 'hello@ifytravels.com'); ?></p>
                </div>

                <!-- Phone Card -->
                <div class="glass-form !p-8 !bg-white/5 border border-white/10 rounded-3xl hover:border-secondary/30 transition group">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-secondary mb-6 group-hover:bg-secondary group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-white mb-2">Call Anytime</h3>
                    <p class="text-gray-400 font-light"><?php echo get_setting('contact_phone', '+91 987 654 3210'); ?></p>
                </div>

            </div>

            <!-- Contact Form -->
            <div class="lg:w-2/3">
                <div class="glass-form !p-10 !bg-white/5 border border-white/10 rounded-3xl shadow-2xl relative overflow-hidden">
                    
                    <!-- Decorative Background Element -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full filter blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                    <?php if ($msgSent): ?>
                        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-3xl font-heading font-bold text-white mb-2">Message Received</h3>
                            <p class="text-gray-400 mb-8 max-w-md">Thank you for contacting us. A member of our concierge team will respond shortly.</p>
                            <a href="<?php echo base_url('pages/contact.php'); ?>" class="glass-button text-sm">Send Another Message</a>
                        </div>
                    <?php else: ?>
                        <h2 class="text-3xl font-heading font-bold text-white mb-8 border-b border-white/10 pb-6">Send a Message</h2>
                        
                        <?php if ($errorMsg): ?>
                            <div class="bg-red-500/20 text-red-200 p-4 rounded-xl mb-6 border border-red-500/30">
                                <?php echo htmlspecialchars($errorMsg); ?>
                            </div>
                        <?php endif; ?>

                        <form id="contact-form" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2 ml-1">Your Name</label>
                                    <input type="text" name="name" required placeholder="John Doe" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2 ml-1">Email Address</label>
                                    <input type="email" name="email" required placeholder="john@example.com" class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-gray-400 text-sm mb-2 ml-1">Subject</label>
                                <select name="subject" class="glass-input w-full !bg-white/5 !border-white/10 text-white [&>option]:text-charcoal focus:!border-secondary transition">
                                    <option>General Inquiry</option>
                                    <option>Bespoke Travel Planning</option>
                                    <option>Corporate Partnership</option>
                                    <option>Press & Media</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-400 text-sm mb-2 ml-1">How can we help?</label>
                                <textarea name="message" rows="5" required placeholder="Tell us about your travel plans..." class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-secondary to-yellow-600 hover:to-secondary text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 transform hover:-translate-y-1">
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