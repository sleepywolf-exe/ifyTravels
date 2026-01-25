<?php
// pages/partner-program.php
$pageTitle = "Partner Program";
$pageDescription = "Join the ifyTravels Partner Program and earn commissions by sharing our exclusive travel packages.";
// $pageImage = 'assets/images/partner-share.jpg'; 

require_once __DIR__ . '/../includes/functions.php';

$successMsg = '';
$errorMsg = '';
$generatedLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security check failed. Please refresh the page and try again.");
    }
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

        // 1. Generate Unique Code
        $code = '';
        if (!empty($customCode)) {
            // Clean custom code: Uppercase, Alphanumeric only
            $cleanCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $customCode));

            // Check availability
            $exists = $db->fetch("SELECT id FROM affiliates WHERE code = ?", [$cleanCode]);
            if ($exists) {
                $errorMsg = "The code '$cleanCode' is already taken. Please try another.";
            } else {
                $code = $cleanCode;
            }
        } else {
            // Auto-generate Random String (8 chars)
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[rand(0, strlen($chars) - 1)];
            }

            // Minimal collision check (retry once)
            if ($db->fetch("SELECT id FROM affiliates WHERE code = ?", [$code])) {
                $code = ''; // Regenerate
                for ($i = 0; $i < 8; $i++) {
                    $code .= $chars[rand(0, strlen($chars) - 1)];
                }
            }
        }

        if (empty($errorMsg)) {
            try {
                // 2. Insert into DB with Password Hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO affiliates (name, email, code, status, password_hash) VALUES (?, ?, ?, 'active', ?)";
                if ($db->execute($sql, [$name, $email, $code, $hash])) {
                    $generatedLink = base_url("?ref=$code");
                    $successMsg = "Welcome to the family, $name!";

                    // Link to dashboard
                    $successMsg .= "<br><a href='" . base_url('partner/login.php') . "' class='text-primary underline mt-2 inline-block'>Login to Dashboard</a>";

                    // Send Welcome Email
                    send_partner_welcome_email($email, $name);
                } else {
                    $errorMsg = "Database Error: Unable to register. Please contact support.";
                    error_log("Partner Signup Failed: Database insert returned false.");
                }

            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMsg = "This email or code is already registered.";
                } else {
                    $errorMsg = "Something went wrong. Please try again.";
                    error_log("Partner Signup Error: " . $e->getMessage());
                }
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="relative pt-32 pb-12 bg-cover bg-center min-h-[400px] flex items-center justify-center"
    style="background-image: url('<?php echo base_url('assets/images/destinations/maldives.jpg'); ?>');">
    <!-- Fallback image -->
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 font-heading">Partner Program</h1>
        <p class="text-gray-100 max-w-2xl mx-auto text-lg">Turn your passion for travel into earnings. Join our network
            of influencers and start earning today.</p>
    </div>
</div>

<div class="container mx-auto px-6 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

        <!-- Info Section -->
        <div>
            <h2 class="text-3xl font-bold text-charcoal mb-6 font-heading">Why Join Us?</h2>
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="bg-primary/10 p-3 rounded-lg mr-4 text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-charcoal mb-2">Attractive Commission</h3>
                        <p class="text-gray-600">Earn competitive commissions on every booking made through your unique
                            link.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-primary/10 p-3 rounded-lg mr-4 text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-charcoal mb-2">30-Day Tracking</h3>
                        <p class="text-gray-600">Our cookies last for 30 days. You get credit even if they book weeks
                            later.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-primary/10 p-3 rounded-lg mr-4 text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-charcoal mb-2">Easy Onboarding</h3>
                        <p class="text-gray-600">Sign up in seconds and get your link instantly. No waiting period.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form / Success Section -->
        <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
            <?php if (!empty($successMsg)): ?>
                <div class="text-center py-8">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-charcoal mb-2">
                        <?php echo $successMsg; ?>
                    </h3>
                    <p class="text-gray-600 mb-6">Your unique affiliate link is ready!</p>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 break-all">
                        <code class="text-primary font-bold text-lg"><?php echo $generatedLink; ?></code>
                    </div>

                    <button onclick="navigator.clipboard.writeText('<?php echo $generatedLink; ?>'); alert('Link copied!');"
                        class="bg-charcoal text-white px-6 py-3 rounded-lg font-bold hover:bg-black transition w-full">
                        Copy Link
                    </button>

                    <p class="text-sm text-gray-500 mt-4">Save this link. Share it on your social media, blog, or videos.
                    </p>
                </div>
            <?php else: ?>
                <h3 class="text-2xl font-bold text-charcoal mb-6">Join the Program</h3>

                <?php if (!empty($errorMsg)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100 text-sm">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="e.g. Rahul Sharma">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="you@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Phone (Optional)</label>
                        <input type="tel" name="phone"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="+91 98765 43210">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Create Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="Min. 8 characters">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Preferred Code (Optional)</label>
                        <input type="text" name="custom_code"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="e.g. RAHUL2024">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-teal-700 transition shadow-lg transform hover:-translate-y-0.5">
                        Create My Affiliate Link
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>