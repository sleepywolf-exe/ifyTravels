<?php
// data/loader.php - Centralized data loader with caching

require_once __DIR__ . '/../includes/db.php';

$db = Database::getInstance();

// Cache arrays
static $destinations = null;
static $packages = null;

/**
 * Load destinations from database (cached)
 */
function loadDestinations()
{
    global $destinations, $db;

    if ($destinations !== null) {
        return $destinations;
    }

    try {
        $rawDestinations = $db->fetchAll("SELECT * FROM destinations ORDER BY is_new DESC, is_featured DESC, created_at DESC");
    } catch (Exception $e) {
        // Fallback for missing column
        $rawDestinations = $db->fetchAll("SELECT * FROM destinations ORDER BY is_featured DESC, created_at DESC");
    }

    // Map DB keys to frontend keys
    $destinations = array_map(function ($d) {
        $d['image'] = $d['image_url'];
        return $d;
    }, $rawDestinations);

    return $destinations;
}

/**
 * Get destination by ID
 */
function getDestinationById($id)
{
    global $db;

    $dest = $db->fetch("SELECT * FROM destinations WHERE id = ?", [$id]);
    if ($dest) {
        $dest['image'] = $dest['image_url'];
    }
    return $dest;
}

/**
 * Load packages from database (cached)
 */
function loadPackages()
{
    global $packages, $db;

    if ($packages !== null) {
        return $packages;
    }

    try {
        $fetchedPackages = $db->fetchAll("SELECT * FROM packages ORDER BY is_new DESC, is_popular DESC, created_at DESC");
    } catch (Exception $e) {
        // Fallback
        $fetchedPackages = $db->fetchAll("SELECT * FROM packages ORDER BY is_popular DESC, created_at DESC");
    }

    // Process JSON fields & Map Keys
    $packages = array_map(function ($pkg) {
        $pkg['features'] = !empty($pkg['features']) ? json_decode($pkg['features'], true) : [];
        $pkg['inclusions'] = !empty($pkg['inclusions']) ? json_decode($pkg['inclusions'], true) : [];
        $pkg['exclusions'] = !empty($pkg['exclusions']) ? json_decode($pkg['exclusions'], true) : [];
        $pkg['trust_badges'] = (isset($pkg['trust_badges']) && !empty($pkg['trust_badges'])) ? json_decode($pkg['trust_badges'], true) : [];
        $pkg['activities'] = (isset($pkg['activities']) && !empty($pkg['activities'])) ? json_decode($pkg['activities'], true) : [];
        $pkg['themes'] = (isset($pkg['themes']) && !empty($pkg['themes'])) ? json_decode($pkg['themes'], true) : [];
        $pkg['destinationId'] = $pkg['destination_id'];
        $pkg['image'] = $pkg['image_url'];
        $pkg['isPopular'] = $pkg['is_popular'] ? true : false;
        return $pkg;
    }, $fetchedPackages);

    return $packages;
}

/**
 * Get packages by destination
 */
function getPackagesByDestination($destId)
{
    $packages = loadPackages();
    return array_filter($packages, function ($p) use ($destId) {
        return $p['destination_id'] == $destId;
    });
}

/**
 * Get package by ID
 */
function getPackageById($id)
{
    global $db;

    $pkg = $db->fetch("SELECT * FROM packages WHERE id = ?", [$id]);
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
 * Load testimonials from database
 */
function loadTestimonials($limit = 3)
{
    try {
        $db = Database::getInstance();
        $result = $db->fetchAll("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT ?", [$limit]);

        // Log for debugging
        if (empty($result)) {
            error_log("loadTestimonials: Query returned 0 results");
        } else {
            error_log("loadTestimonials: Found " . count($result) . " testimonials");
        }

        return $result;
    } catch (Exception $e) {
        error_log("loadTestimonials ERROR: " . $e->getMessage());
        return [];
    }
}

// Initialize data when this file is included
$destinations = loadDestinations();
$packages = loadPackages();
?>