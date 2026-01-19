<?php
$pageTitle = "Contact Us";
include __DIR__ . '/../includes/header.php'; // Includes db.php/functions.php
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact Us",
  "description": "Get in touch with ifyTravels for bookings, inquiries, and support.",
  "url": "<?php echo base_url('contact'); ?>"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "<?php echo base_url(); ?>"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "Contact",
    "item": "<?php echo base_url('contact'); ?>"
  }]
}
</script>

<!-- Link Glassmorphism CSS -->
<link rel="stylesheet" href="../assets/css/glassmorphism.css">

<?php

$msgSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, 'New', ?)");
        $stmt->execute([$name, $email, $subject, $message, date('Y-m-d H:i:s')]);

        // Send Customer Confirmation
        send_lead_confirmation_email($email, $name, get_setting('contact_phone', ''));

        // Send Admin Notification
        $adminData = [
            'Type' => 'General Inquiry (Contact Form)',
            'Subject' => $subject,
            'Name' => $name,
            'Email' => $email,
            'Message' => $message
        ];
        send_admin_notification_email("New Contact Inquiry: $name", $adminData, "View Inquiries", base_url("admin/inquiries.php"));

        $msgSent = true;
    } catch (Exception $e) {
        $error = "Failed to send message.";
    }
}
?>

<!-- Header (Dynamic) -->
<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[300px] flex items-center justify-center"
    style="background-image: url('<?php echo get_setting('contact_bg', base_url('assets/images/destinations/dubai.jpg')); ?>');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl font-bold text-white mb-4">Get in Touch</h1>
        <p class="text-gray-100 max-w-2xl mx-auto">We'd love to hear from you. Send us a message and we'll respond
            as soon as possible.</p>
    </div>
</div>

<div class="container mx-auto px-6 py-12 flex-1">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">

        <!-- Contact Info -->
        <div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 h-full">
                <h3 class="text-2xl font-bold mb-6 text-primary">Contact Information</h3>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Our Office</h4>
                            <p class="text-gray-600"><?php echo get_setting('address', '123 Travel Lane, Delhi'); ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-green-50 p-3 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Email Us</h4>
                            <p class="text-gray-600"><?php echo get_setting('contact_email', 'hello@ifytravels.com'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-purple-50 p-3 rounded-lg text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Call Us</h4>
                            <p class="text-gray-600"><?php echo get_setting('contact_phone', '+91 987 654 3210'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="glass-form p-8"
            style="background: linear-gradient(135deg, #0F766E 0%, #0d9488 100%); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <?php if ($msgSent): ?>
                <div class="bg-green-100 text-green-700 p-6 rounded-lg text-center">
                    <h3 class="font-bold text-xl mb-2">Message Sent!</h3>
                    <p>Thank you for reaching out. We will get back to you shortly.</p>
                    <a href="contact.php" class="inline-block mt-4 text-primary font-bold hover:underline">Send another</a>
                </div>
            <?php else: ?>
                <form id="contact-form" method="POST" class="space-y-6">
                    <div>
                        <label class="glass-label">Your Name</label>
                        <input type="text" name="name" required placeholder="Enter your full name"
                            class="glass-input w-full">
                    </div>
                    <div>
                        <label class="glass-label">Email Address</label>
                        <input type="email" name="email" required placeholder="your@email.com" class="glass-input w-full">
                    </div>
                    <div>
                        <label class="glass-label">Subject</label>
                        <select name="subject" class="glass-select w-full">
                            <option>General Inquiry</option>
                            <option>Booking Issue</option>
                            <option>Partnership</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="glass-label">Message</label>
                        <textarea name="message" rows="4" required placeholder="Write your message here..."
                            class="glass-textarea w-full"></textarea>
                    </div>
                    <button type="submit" class="glass-button w-full">
                        Send Message
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>