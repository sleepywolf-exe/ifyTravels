<?php
$pageTitle = "Contact Concierge - ifyTravels";
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

    <!-- Hero Background (Parallax) -->
    <div class="absolute inset-0 min-h-[50vh] md:h-[70vh] z-0 overflow-hidden">
        <img src="<?php echo get_setting('contact_bg', base_url('assets/images/destinations/dubai.jpg')); ?>"
            class="w-full h-full object-cover brightness-[0.4] scale-105 animate-slow-pan" alt="Contact Background">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-slate-50"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 pt-32 md:pt-52 pb-12 md:pb-24">

        <!-- Header -->
        <div class="text-center mb-12 md:mb-20 animate-fade-in-up">
            <span
                class="inline-block px-4 py-1.5 rounded-full border border-white/20 bg-white/10 backdrop-blur-md text-white/90 text-[11px] font-bold tracking-[0.2em] uppercase mb-6">
                24/7 Concierge
            </span>
            <h1
                class="text-4xl md:text-7xl lg:text-8xl font-heading font-bold text-white mb-6 md:mb-8 drop-shadow-2xl tracking-tight">
                Get in <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-white">Touch</span>
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto text-base md:text-xl font-light leading-relaxed drop-shadow-lg">
                Whether you're planning a grand tour or a quick getaway, our concierge team is here to orchestrate every
                detail.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-16 max-w-7xl mx-auto items-start">

            <!-- Contact Information Cards -->
            <div class="lg:w-1/3 space-y-6 w-full animate-fade-in-up delay-100">

                <!-- Card: Visit -->
                <div
                    class="bg-white/90 backdrop-blur-xl border border-white/50 rounded-[2rem] p-8 shadow-xl hover:shadow-2xl transition duration-500 group relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                    </div>
                    <div
                        class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Visit Our Lounge</h3>
                    <p class="text-slate-500 leading-relaxed font-light">
                        <?php echo get_setting('address', '123 Travel Lane, Delhi'); ?><br>
                        <span class="text-xs font-bold text-primary uppercase tracking-wider mt-2 block">Open Mon-Sat,
                            9am - 8pm</span>
                    </p>
                </div>

                <!-- Card: Email -->
                <div
                    class="bg-white/90 backdrop-blur-xl border border-white/50 rounded-[2rem] p-8 shadow-xl hover:shadow-2xl transition duration-500 group relative overflow-hidden">
                    <a href="mailto:<?php echo get_setting('contact_email', 'hello@ifytravels.com'); ?>"
                        class="absolute inset-0 z-10"></a>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                    </div>
                    <div
                        class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-envelope-open-text text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Email Concierge</h3>
                    <p class="text-slate-500 font-light group-hover:text-secondary transition-colors">
                        <?php echo get_setting('contact_email', 'hello@ifytravels.com'); ?>
                    </p>
                    <div
                        class="flex items-center gap-2 mt-4 text-xs font-bold text-slate-400 group-hover:text-secondary transition-colors">
                        <span>Drop us a line</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <!-- Card: Phone -->
                <div
                    class="bg-blue-600 text-white border border-blue-500 rounded-[2rem] p-8 shadow-xl shadow-blue-200 hover:shadow-2xl hover:shadow-blue-300 transition duration-500 group relative overflow-hidden">
                    <a href="tel:<?php echo get_setting('contact_phone', '+91 987 654 3210'); ?>"
                        class="absolute inset-0 z-10"></a>
                    <div class="absolute -bottom-10 -right-10 text-white/10 text-9xl">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div
                        class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm">
                        <i class="fas fa-phone-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-white mb-2">Call Anytime</h3>
                    <p class="text-white/90 font-light text-lg">
                        <?php echo get_setting('contact_phone', '+91 987 654 3210'); ?>
                    </p>
                    <div
                        class="flex items-center gap-2 mt-4 text-xs font-bold text-white/60 group-hover:text-white transition-colors">
                        <span>Call now</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

            </div>

            <!-- Contact Form -->
            <div class="lg:w-2/3 w-full animate-fade-in-up delay-200">
                <div
                    class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-6 md:p-12 relative overflow-hidden border border-slate-100">

                    <!-- Form Background Art -->
                    <div
                        class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-primary/5 to-secondary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none">
                    </div>

                    <?php if ($msgSent): ?>
                        <div class="flex flex-col items-center justify-center min-h-[500px] text-center">
                            <div
                                class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl md:text-4xl font-heading font-bold text-slate-900 mb-4">Request Received
                            </h3>
                            <p class="text-slate-500 mb-10 max-w-md text-lg leading-relaxed">
                                Thank you for choosing ifyTravels. A dedicated travel designer will review your request and
                                contact you shortly.
                            </p>
                            <a href="<?php echo base_url('pages/contact.php'); ?>"
                                class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all">
                                Send Another Message
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="relative z-10">
                            <h2 class="text-3xl md:text-4xl font-heading font-bold text-slate-900 mb-2">
                                Send a Message
                            </h2>
                            <p class="text-slate-500 mb-10">Tell us about your dream trip, and we'll make it happen.</p>

                            <?php if ($errorMsg): ?>
                                <div
                                    class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 border border-red-100 flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                                    <?php echo htmlspecialchars($errorMsg); ?>
                                </div>
                            <?php endif; ?>

                            <form id="contact-form" method="POST" class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="group">
                                        <label
                                            class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2 group-focus-within:text-primary transition-colors">Your
                                            Name</label>
                                        <input type="text" name="name" required placeholder="John Doe"
                                            class="w-full px-0 py-3 bg-transparent border-b-2 border-slate-200 text-slate-900 placeholder-slate-300 focus:outline-none focus:border-primary transition-colors text-lg font-medium">
                                    </div>
                                    <div class="group">
                                        <label
                                            class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2 group-focus-within:text-primary transition-colors">Email
                                            Address</label>
                                        <input type="email" name="email" required placeholder="john@example.com"
                                            class="w-full px-0 py-3 bg-transparent border-b-2 border-slate-200 text-slate-900 placeholder-slate-300 focus:outline-none focus:border-primary transition-colors text-lg font-medium">
                                    </div>
                                </div>

                                <div class="group">
                                    <label
                                        class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2 group-focus-within:text-primary transition-colors">Subject</label>
                                    <div class="relative">
                                        <select name="subject"
                                            class="w-full px-0 py-3 bg-transparent border-b-2 border-slate-200 text-slate-900 focus:outline-none focus:border-primary transition-colors text-lg font-medium appearance-none cursor-pointer">
                                            <option>General Inquiry</option>
                                            <option>Bespoke Travel Planning</option>
                                            <option>Corporate Partnership</option>
                                            <option>Press & Media</option>
                                        </select>
                                        <div
                                            class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label
                                        class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2 group-focus-within:text-primary transition-colors">How
                                        can we help?</label>
                                    <textarea name="message" rows="4" required
                                        placeholder="Tell us about your travel plans, preferences, or any specific requirements..."
                                        class="w-full px-0 py-3 bg-transparent border-b-2 border-slate-200 text-slate-900 placeholder-slate-300 focus:outline-none focus:border-primary transition-colors text-lg font-medium resize-none leading-relaxed"></textarea>
                                </div>

                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full bg-slate-900 text-white font-bold py-5 rounded-xl shadow-lg hover:bg-primary transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg group">
                                        <span>Send Request</span>
                                        <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes slow-pan {
        0% {
            transform: scale(1.05);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1.05);
        }
    }

    .animate-slow-pan {
        animation: slow-pan 20s infinite ease-in-out;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>