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
function base_url($path = '')
{
    // Return absolute path from root to handle all URL depths (router/slugs)
    return '/' . ltrim($path, '/');
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
    return '/destinations/' . $slug;
}

/**
 * Get slug URL for packages
 */
function package_url($slug)
{
    return '/packages/' . $slug;
}
?>