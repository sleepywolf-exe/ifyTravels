<?php
/**
 * Router script for PHP built-in server (php -S localhost:8000 router.php)
 * Handles SEO-friendly slug-based URLs and redirects
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString ?? '', $queryParams);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle sitemap.xml
if ($requestUri === '/sitemap.xml') {
    include 'sitemap.php';
    exit;
}

// Handle static files (let PHP serve them directly)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|webp|svg|ico|woff|woff2|ttf|pdf|mp4)$/i', $requestUri)) {
    return false; // Serve the requested file as-is
}

// Redirect index.php to root
if ($requestUri === '/index.php') {
    header('Location: /', true, 301);
    exit;
}

// Redirect old ID-based URLs to slug-based URLs
if (preg_match('#^/(pages/)?destination-details\.php$#', $requestUri) && isset($queryParams['id'])) {
    include 'destination-redirect.php';
    exit;
}

if (preg_match('#^/(pages/)?package-details\.php$#', $requestUri) && isset($queryParams['id'])) {
    include 'package-redirect.php';
    exit;
}

// Handle clean slug-based destination URLs: /destinations/bali
if (preg_match('#^/destinations/([a-z0-9\-]+)/?$#', $requestUri, $matches)) {
    $_GET['slug'] = $matches[1];
    include __DIR__ . '/pages/destination-details.php';
    exit;
}

// Handle clean slug-based package URLs: /packages/baku-explorer
if (preg_match('#^/packages/([a-z0-9\-]+)/?$#', $requestUri, $matches)) {
    $_GET['slug'] = $matches[1];
    include __DIR__ . '/pages/package-details.php';
    exit;
}

// Route clean URLs to corresponding pages
$routes = [
    '/destinations' => '/pages/destinations.php',
    '/packages' => '/pages/packages.php',
    '/contact' => '/pages/contact.php',
    '/booking' => '/pages/booking.php',
];

// Remove trailing slash for matching
$cleanUri = rtrim($requestUri, '/');

if (isset($routes[$cleanUri])) {
    // Preserve query string
    $_SERVER['REQUEST_URI'] = $routes[$cleanUri] . ($queryString ? '?' . $queryString : '');
    include __DIR__ . $routes[$cleanUri];
    exit;
}

// Redirect /pages/... URLs to clean URLs (301 Permanent Redirect)
if (preg_match('#^/pages/(destinations|packages|contact|booking)\.php$#', $requestUri, $matches)) {
    $cleanUrl = '/' . $matches[1];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $cleanUrl . ($queryString ? '?' . $queryString : ''));
    exit;
}

// Default: Let PHP serve the requested file normally
return false;
