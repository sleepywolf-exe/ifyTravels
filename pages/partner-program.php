<?php
// pages/partner-program.php
$pageTitle = "Partner Program";
$pageDescription = "Join the ifyTravels Partner Program and earn commissions by sharing our exclusive travel packages.";
$pageKeywords = "Travel Affiliate Program, Earn Passive Income, Travel Partner, ifyTravels Affiliate, iflytravel partner, Travel Commission";


require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/classes/SpamProtection.php';

$successMsg = '';
$errorMsg = '';
$generatedLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security check failed. Please refresh the page and try again.");
    }

    // Anti-Spam Check
    if (class_exists('SpamProtection') && SpamProtection::isSpam($_POST)) {
        $errorMsg = "System detected potential spam behavior. Please wait a few seconds and try again.";
        // Stop processing
    } else {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $customCode = sanitize_input($_POST['custom_code']);
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $errorMsg = "Name, Email, and Password are required.";
        } elseif (strlen($password) < 8) {
            $errorMsg = "Password must be at least 8 characters.";
        } else {
            $db = Database::getInstance();
            $code = '';
            // ... (rest of logic) ...
            if (!empty($customCode)) {
                $cleanCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $customCode));
                $exists = $db->fetch("SELECT id FROM affiliates WHERE code = ?", [$cleanCode]);
                if ($exists) {
                    $errorMsg = "The code '$cleanCode' is already taken. Please try another.";
                } else {
                    $code = $cleanCode;
                }
            } else {
                $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $code = '';
                for ($i = 0; $i < 8; $i++)
                    $code .= $chars[rand(0, strlen($chars) - 1)];
                if ($db->fetch("SELECT id FROM affiliates WHERE code = ?", [$code])) {
                    $code = '';
                    for ($i = 0; $i < 8; $i++)
                        $code .= $chars[rand(0, strlen($chars) - 1)];
                }
            }

            if (empty($errorMsg)) {
                try {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO affiliates (name, email, code, status, password_hash) VALUES (?, ?, ?, 'active', ?)";
                    if ($db->execute($sql, [$name, $email, $code, $hash])) {
                        $generatedLink = base_url("?ref=$code");
                        $successMsg = "Welcome to the family, $name!";
                        send_partner_welcome_email($email, $name);
                    } else {
                        $errorMsg = "Database Error: Unable to register. Please contact support.";
                    }
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errorMsg = "This email or code is already registered.";
                    } else {
                        $errorMsg = "Something went wrong. Please try again.";
                    }
                }
            }
        }
    }
}
?>

<div class="relative min-h-screen bg-slate-50 text-slate-900">


    <!-- Hero Section -->
    <div class="relative h-[60vh] flex items-center justify-center overflow-hidden">
        <img src="<?php echo base_url('assets/images/destinations/dubai.jpg'); ?>"
            class="absolute inset-0 w-full h-full object-cover opacity-20 animate-scale-slow parallax-bg"
            alt="Partner Program">
        <div class="absolute inset-0 bg-gradient-to-b from-white/60 via-white/40 to-slate-50"></div>

        <div class="container mx-auto px-6 relative z-10 text-center animate-fade-in-up pt-10">
            <span class="text-secondary font-bold tracking-widest uppercase text-xs mb-4 block">Exclusive Network</span>
            <h1 class="text-4xl md:text-7xl font-heading font-bold text-slate-900 mb-6 drop-shadow-sm">Partner Program
            </h1>
            <p class="text-slate-600 max-w-2xl mx-auto text-lg font-light leading-relaxed">
                Join an elite community of travel curators. Share exceptional journeys and earn premium rewards.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-16 relative z-10 -mt-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <!-- Benefits -->
            <div class="space-y-8 animate-fade-in-up delay-100">
                <div
                    class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl hover:shadow-2xl transition group">
                    <div
                        class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Lucrative Commissions</h3>
                    <p class="text-slate-500 font-light">Earn competitive rates on every bespoke package booked through
                        your referral.</p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl hover:shadow-2xl transition group">
                    <div
                        class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">30-Day Cookies</h3>
                    <p class="text-slate-500 font-light">We value your influence. Receive credit for bookings made
                        within 30 days of the first click.</p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl hover:shadow-2xl transition group">
                    <div
                        class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-2">Instant Access</h3>
                    <p class="text-slate-500 font-light">Sign up and get your unique referral code immediately. No
                        waiting, just earning.</p>
                </div>
            </div>

            <!-- Application Form -->
            <div
                class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-6 md:p-10 animate-fade-in-up delay-200">
                <?php if (!empty($successMsg)): ?>
                    <div class="text-center py-10">
                        <div
                            class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-heading font-bold text-slate-900 mb-2"><?php echo $successMsg; ?></h3>
                        <p class="text-slate-500 mb-8">Your unique affiliate link is ready.</p>

                        <div class="bg-slate-100 p-4 rounded-xl border border-slate-200 mb-6 break-all">
                            <code class="text-secondary font-mono text-lg"><?php echo $generatedLink; ?></code>
                        </div>

                        <button
                            onclick="navigator.clipboard.writeText('<?php echo $generatedLink; ?>'); alert('Link copied!');"
                            class="w-full bg-white border border-slate-200 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-50 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                </path>
                            </svg>
                            Copy Link
                        </button>

                        <a href="<?php echo base_url('partner/login.php'); ?>"
                            class="block mt-6 text-sm text-primary hover:underline transition">Access Dashboard &rarr;</a>
                    </div>
                <?php else: ?>
                    <h3 class="text-2xl font-heading font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4">Become a
                        Partner</h3>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100 text-sm">
                            <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-5">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <?php if (class_exists('SpamProtection'))
                            echo SpamProtection::generateFields(); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Full Name</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400"
                                    placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Email Address</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400"
                                    placeholder="john@example.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Phone (Optional)</label>
                            <input type="tel" name="phone"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400"
                                placeholder="+91 98765 43210">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Create Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400"
                                placeholder="Min. 8 characters">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Preferred Code
                                (Optional)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-slate-500 font-mono text-sm">REF-</span>
                                <input type="text" name="custom_code"
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition uppercase placeholder-slate-400"
                                    placeholder="MYCODE">
                            </div>
                            <p class="text-xs text-slate-500 mt-2 ml-1">Leave blank to auto-generate.</p>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-secondary to-yellow-600 hover:to-secondary text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 transform hover:-translate-y-1 magnetic-btn">
                            Join Now
                        </button>
                        <p class="text-center text-xs text-slate-500">By joining, you agree to our Terms & Conditions.</p>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>