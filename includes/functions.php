<?php
// includes/functions.php - Core utility functions

// Start session if not already started (safe wrapper)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database
require_once __DIR__ . '/db.php';

// Get singleton DB instance
$db = Database::getInstance();
$pdo = $db->getConnection();

// Cache for settings to avoid repeated queries
$globalSettings = null;

// Global Exception Handler
set_exception_handler(function ($e) {
    error_log("Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // Don't show details in production
    if (ini_get('display_errors')) {
        echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
    } else {
        if (file_exists(__DIR__ . '/../pages/503.php')) {
            include __DIR__ . '/../pages/503.php';
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
    exit;
});

/**
 * Get site setting by key
 */
function get_setting($key, $default = '')
{
    global $globalSettings, $db;

    // Load settings once and cache
    if ($globalSettings === null) {
        $globalSettings = [];
        $results = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
        foreach ($results as $row) {
            $globalSettings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $globalSettings[$key] ?? $default;
}

/**
 * Generate base URL for assets and pages
 */
/**
 * Generate base URL for assets and pages (Optimized)
 */
function base_url($path = '')
{
    static $baseUrl = null;

    if ($baseUrl === null) {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];

        // Simple and fast root detection
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = str_replace('\\', '/', $scriptDir);

        // Remove known subdirs to find true root
        $remove = ['/admin', '/pages', '/services', '/includes', '/seo', '/api', '/partner'];
        foreach ($remove as $dir) {
            if (substr($scriptDir, -strlen($dir)) === $dir) {
                $scriptDir = substr($scriptDir, 0, -strlen($dir));
                break; // Assume only one level deep for these
            }
        }

        $baseUrl = $protocol . $host . rtrim($scriptDir, '/') . '/';
    }

    // Return absolute URL
    if (strpos($path, 'http') === 0) {
        return $path;
    }

    return $baseUrl . ltrim($path, '/');
}

/**
 * Get current year for footer
 */
function current_year()
{
    return date('Y');
}

/**
 * Sanitize output to prevent XSS
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input data
 */
function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data ?? '')), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * Check if user is admin
 */
function is_admin()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Generate CSRF Token
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token)
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate SEO-friendly slug from string
 */
function generateSlug($string)
{
    // Convert to lowercase
    $slug = strtolower($string);

    // Replace spaces and special characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    // Remove consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);

    // Trim hyphens from ends
    $slug = trim($slug, '-');

    return $slug;
}

/**
 * Get destination by slug
 */
function getDestinationBySlug($slug)
{
    global $db;
    $dest = $db->fetch("SELECT * FROM destinations WHERE slug = ?", [$slug]);
    if ($dest) {
        $dest['image'] = $dest['image_url'];
    }
    return $dest;
}

/**
 * Get package by slug
 */
function getPackageBySlug($slug)
{
    global $db;
    $pkg = $db->fetch("SELECT * FROM packages WHERE slug = ?", [$slug]);
    if ($pkg) {
        $pkg['features'] = !empty($pkg['features']) ? json_decode($pkg['features'], true) : [];
        $pkg['inclusions'] = !empty($pkg['inclusions']) ? json_decode($pkg['inclusions'], true) : [];
        $pkg['exclusions'] = !empty($pkg['exclusions']) ? json_decode($pkg['exclusions'], true) : [];
        $pkg['trust_badges'] = (isset($pkg['trust_badges']) && !empty($pkg['trust_badges'])) ? json_decode($pkg['trust_badges'], true) : [];
        $pkg['activities'] = (isset($pkg['activities']) && !empty($pkg['activities'])) ? json_decode($pkg['activities'], true) : [];
        $pkg['themes'] = (isset($pkg['themes']) && !empty($pkg['themes'])) ? json_decode($pkg['themes'], true) : [];
        $pkg['destinationId'] = $pkg['destination_id'];
        $pkg['image'] = $pkg['image_url'];
        $pkg['isPopular'] = $pkg['is_popular'] ? true : false;
    }
    return $pkg;
}

/**
 * Get slug URL for destinations
 */
function destination_url($slug)
{
    // Clean SEO URL
    return base_url('destinations/' . $slug);
}

/**
 * Get slug URL for packages
 */
function package_url($slug)
{
    // Clean SEO URL
    return base_url('packages/' . $slug);
}

/**
 * Get SVG Icon for Lead Source
 */
function get_source_icon_svg($source)
{
    $source = strtolower($source ?? '');

    // Icon Definitions
    $icons = [
        'google' => '<svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/></svg>',
        'facebook' => '<svg class="w-4 h-4 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.15 5.96C15.21 5.96 16.12 6.04 16.12 6.04V8.51H15.01C13.77 8.51 13.38 9.28 13.38 10.07V12.06H16.15L15.71 14.96H13.38V21.96C18.16 21.21 21.82 17.06 21.82 12.06C21.82 6.53 17.32 2.04 12 2.04Z"/></svg>',
        'instagram' => '<svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>',
        'linkedin' => '<svg class="w-4 h-4 text-blue-800" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>',
        'twitter' => '<svg class="w-4 h-4 text-sky-400" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10V10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.26,19.13 1.96,19.11 1.66,19.09C3.56,20.29 5.81,21 8.21,21C16.08,21 20.38,14.46 20.38,8.79C20.38,8.6 20.38,8.42 20.37,8.23C21.2,7.65 21.92,6.9 22.46,6Z"/></svg>',
        'youtube' => '<svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
        'direct' => '<svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>',
    ];

    if (strpos($source, 'google') !== false)
        return $icons['google'];
    if (strpos($source, 'face') !== false || strpos($source, 'fb') !== false)
        return $icons['facebook'];
    if (strpos($source, 'insta') !== false || strpos($source, 'ig') !== false)
        return $icons['instagram'];
    if (strpos($source, 'link') !== false || strpos($source, 'in') !== false)
        return $icons['linkedin'];
    if (strpos($source, 'tweet') !== false || strpos($source, 'x') !== false || strpos($source, 'twit') !== false)
        return $icons['twitter'];
    if (strpos($source, 'tube') !== false || strpos($source, 'yt') !== false)
        return $icons['youtube'];

    // Default fallback (globe)
    return '<svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>';
}

/**
 * Send Lead Confirmation Email (Branded HTML)
 */
function send_lead_confirmation_email($toEmail, $customerName, $customerPhone)
{
    $subject = "Inquiry Received - ifyTravels";
    $fromEmail = get_setting('contact_email', 'hello@ifytravels.com');

    // Brand Colors
    $teal = "#0F766E";
    $tealLighter = "#115E59";

    // Email Body
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Confirmation</title>
        <style>
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
            .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 40px; }
            .container { max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
            
            /* Header */
            .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #f0f0f0; }
            .header img { height: 50px; width: auto; }

            /* Content */
            .content { padding: 40px 30px; color: #374151; line-height: 1.6; }
            .h2 { font-size: 22px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 15px; }
            .p {font-size: 16px; margin-bottom: 20px; color: #4B5563; }

            /* Detailed Info Box */
            .info-box { background-color: #F0FDFA; border: 1px solid #CCFBF1; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
            .info-text { font-size: 14px; color: #134E4A; margin: 0; }

            /* Button */
            .btn-container { text-align: center; margin-top: 25px; margin-bottom: 10px; }
            .btn { display: inline-block; background-color: ' . $teal . '; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; letter-spacing: 0.5px; transition: all 0.2s; }
            .btn:hover { background-color: ' . $tealLighter . '; }

            /* Footer */
            .footer { background-color: #F9FAFB; padding: 30px; text-align: center; font-size: 13px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
            .footer p { margin: 5px 0; }
            .footer a { color: ' . $teal . '; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">
                <div class="header">
                    <img src="' . base_url('assets/images/logo-color.png?v=' . time()) . '" alt="ifyTravels">
                </div>
                <div class="content">
                    <h2 class="h2">Hello ' . htmlspecialchars($customerName) . ',</h2>

                    <p class="p">Thank you for choosing ifyTravels! We have successfully received your inquiry and are excited to help you plan your perfect trip.</p>

                    <div class="info-box">
                        <p class="info-text"><strong>Next Steps:</strong> One of our travel experts will review your details and contact you shortly with a personalized itinerary.</p>
                    </div>

                    <p class="p">If you need immediate assistance or want to discuss your plans right now, feel free to give us a call directly.</p>

                    <!-- Call Action Button -->
                    <div class="btn-container">
                        <a href="tel:' . preg_replace('/[^0-9+]/', '', get_setting('contact_phone', '+919999779870')) . '" class="btn">
                            Call Us Now
                        </a>
                    </div>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ifyTravels. All rights reserved.</p>
                    <p>' . htmlspecialchars(get_setting('address', 'Delhi, India')) . '</p>
                    <p><a href="' . base_url() . '">Visit Website</a> | <a href="mailto:support@ifytravels.com">Contact Support</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>';

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ifyTravels <" . $fromEmail . ">" . "\r\n";
    if (filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $headers .= "Reply-To: " . $fromEmail . "\r\n";
    }

    return @mail($toEmail, $subject, $message, $headers);
}

/**
 * Send Admin Notification Email (Table Layout)
 */
function send_admin_notification_email($subject, $data, $actionText = 'View in Admin', $actionUrl = '')
{
    $adminEmail = 'parasasd@gmail.com'; // Admin Email
    $fromEmail = 'no-reply@ifytravels.com';

    // Brand Colors
    $teal = "#0F766E";

    // Build Data Rows
    $rows = '';
    foreach ($data as $label => $value) {
        if (!empty($value)) {
            $rows .= '
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666; width: 40%; font-weight: 500;">' . htmlspecialchars($label) . '</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #111; font-weight: 600;">' . nl2br(htmlspecialchars($value)) . '</td>
            </tr>';
        }
    }

    // Default Action URL
    if (empty($actionUrl)) {
        $actionUrl = base_url('admin/dashboard.php');
    }

    // Email Body
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Admin Notification</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 40px 0; }
            .card { background: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e5e7eb; }
            
            .header { background: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #f3f4f6; }
            .header img { height: 40px; width: auto; }
            
            .subheader { background: #F3F4F6; padding: 15px 30px; text-align: center; border-bottom: 1px solid #e5e7eb; }
            .subheader h2 { margin: 0; font-size: 16px; font-weight: 600; color: #374151; }
            
            .body { padding: 30px; }
            table { width: 100%; border-collapse: collapse; font-size: 15px; }
            
            .btn-container { text-align: center; margin-top: 35px; margin-bottom: 10px; }
            .btn { background: ' . $teal . '; color: white !important; padding: 14px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px rgba(15, 118, 110, 0.2); }
            .btn:hover { background: #0d6e66; }
            
            .footer { text-align: center; padding: 25px; font-size: 12px; color: #9CA3AF; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="header">
                 <img src="' . base_url('assets/images/logo-color.png?v=' . time()) . '" alt="ifyTravels">
            </div>
            <div class="subheader">
                <h2>' . htmlspecialchars($subject) . '</h2>
            </div>
            <div class="body">
                <table>
                    ' . $rows . '
                </table>
                <div class="btn-container">
                    <a href="' . $actionUrl . '" class="btn">' . $actionText . '</a>
                </div>
            </div>
        </div>
        <div class="footer">
             Sent by ifyTravels System • ' . date('d M Y, H:i') . '
        </div>
    </body>
    </html>';

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ifyTravels System <" . $fromEmail . ">" . "\r\n";

    return @mail($adminEmail, $subject, $message, $headers);
}

/**
 * Send Booking Confirmation Email (Detailed HTML)
 */
function send_booking_confirmation_email($toEmail, $customerName, $bookingDetails)
{
    $subject = "Booking Confirmation - ifyTravels";
    $fromEmail = get_setting('contact_email', 'hello@ifytravels.com');
    $teal = "#0F766E";

    // Build Items List
    $itemsHtml = '';
    foreach ($bookingDetails as $label => $val) {
        if (!empty($val)) {
            $itemsHtml .= '<tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #eee; color: #666;">' . htmlspecialchars($label) . '</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #eee; color: #111; font-weight: 600; text-align: right;">' . htmlspecialchars($val) . '</td>
            </tr>';
        }
    }

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: "Segoe UI", sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
            .container { max-width: 600px; background: #fff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
            .header { background: ' . $teal . '; padding: 30px; text-align: center; color: white; }
            .content { padding: 30px; }
            .h2 { margin: 0 0 10px; color: #111; }
            .p { color: #555; line-height: 1.6; }
            .btn { display: inline-block; background: ' . $teal . '; color: #fff !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
            .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin:0; font-size: 24px;">Booking Confirmed</h1>
            </div>
            <div class="content">
                <h2 class="h2">Hi ' . htmlspecialchars($customerName) . ',</h2>
                <p class="p">We are thrilled to confirm your booking request! Our team is reviewing the details and will contact you shortly to finalize everything.</p>
                
                <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                    ' . $itemsHtml . '
                </table>

                <p class="p">If you have any questions, simply reply to this email or call us.</p>
                <div style="text-align: center;">
                    <a href="' . base_url('contact.php') . '" class="btn">View Support</a>
                </div>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' ifyTravels. All rights reserved.
            </div>
        </div>
    </body>
    </html>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ifyTravels <" . $fromEmail . ">" . "\r\n";

    return @mail($toEmail, $subject, $message, $headers);
}

/**
 * Send Partner Welcome Email
 */
function send_partner_welcome_email($toEmail, $name)
{
    $subject = "Welcome to ifyTravels Partner Program!";
    $fromEmail = get_setting('contact_email', 'partners@ifytravels.com');

    $message = "
    Hello $name,

    Welcome to the ifyTravels Partner Program! We are excited to have you on board.

    Your application has been received and is currently active. You can log in to your dashboard to get your tracking link and viewing your performance.

    Login here: " . base_url('partner/login.php') . "

    If you need any help, feel free to reach out.

    Best regards,
    ifyTravels Team
    ";

    $headers = "From: ifyTravels Partners <" . $fromEmail . ">";

    return @mail($toEmail, $subject, $message, $headers);
}

/**
 * Send Partner Approval Email
 */
function send_partner_approval_email($toEmail, $name)
{
    $subject = "Your Partner Account is Active!";
    $fromEmail = get_setting('contact_email', 'partners@ifytravels.com');

    $message = "
    Hello $name,

    Great news! Your partner account has been fully activated.

    You can now access all features of your partner dashboard.

    Login Now: " . base_url('partner/login.php') . "

    Let's grow together!
    ifyTravels Team
    ";

    $headers = "From: ifyTravels Partners <" . $fromEmail . ">";

    return @mail($toEmail, $subject, $message, $headers);
}

/**
 * Send Password Reset Email
 */
function send_password_reset_email($toEmail, $token)
{
    $subject = "Reset Your Password - ifyTravels";
    $fromEmail = get_setting('contact_email', 'support@ifytravels.com');
    $resetLink = base_url('partner/reset_password.php?token=' . $token);

    $message = "
    Click the link below to reset your partner account password:
    
    $resetLink

    This link will expire in 1 hour.
    
    If you didn't request this, please ignore this email.
    ";

    $headers = "From: ifyTravels Support <" . $fromEmail . ">";

    return @mail($toEmail, $subject, $message, $headers);
}
?>