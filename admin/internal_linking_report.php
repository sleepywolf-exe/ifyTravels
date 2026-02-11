<?php
// admin/internal_linking_report.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/classes/InternalLinker.php';

$pageTitle = "Internal Linking Report";
// Mock Header Include for simple report
echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>' . $pageTitle . '</title>';
echo '<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">';
echo '</head><body class="bg-gray-100 p-8">';

$linker = InternalLinker::getInstance();
$db = Database::getInstance();

echo '<div class="max-w-6xl mx-auto bg-white p-8 rounded shadow">';
echo '<h1 class="text-3xl font-bold mb-6 text-indigo-700">Semantic Internal Linking Report</h1>';

// 1. Identify Orphaned Pages (Pages with NO incoming internal links)
// Note: In a real crawler, we would scan content. Here we simulate by checking if the page slug is mentioned in other descriptions.
// For now, we will report pages that are NOT part of any 'Cluster' in our manual logic or have 0 conceptual links.

echo '<h2 class="text-xl font-bold mb-4 border-b pb-2">🚨 Orphaned Page Candidates (Zero Inbound Context)</h2>';

$destinations = $db->fetchAll("SELECT id, name, slug FROM destinations");
$packages = $db->fetchAll("SELECT id, title, slug FROM packages");

$orphans = [];

// Rough check: Is this destination linked from any package?
foreach ($destinations as $dest) {
    // Check if any package mentions this destination name in its description
    $mentions = $db->fetch("SELECT count(*) as c FROM packages WHERE description LIKE ?", ['%' . $dest['name'] . '%']);
    if ($mentions['c'] == 0) {
        $orphans[] = ['type' => 'Destination', 'name' => $dest['name'], 'url' => destination_url($dest['slug'])];
    }
}

// Rough check: Is this package linked from any blog post?
foreach ($packages as $pkg) {
    // Check if any blog mentions this package title
    $mentions = $db->fetch("SELECT count(*) as c FROM posts WHERE content LIKE ?", ['%' . $pkg['title'] . '%']);
    if ($mentions['c'] == 0) {
        // Also check if destination description mentions it
        $destMentions = $db->fetch("SELECT count(*) as c FROM destinations WHERE description LIKE ?", ['%' . $pkg['title'] . '%']);
        if ($destMentions['c'] == 0) {
            $orphans[] = ['type' => 'Package', 'name' => $pkg['title'], 'url' => package_url($pkg['slug'])];
        }
    }
}

if (empty($orphans)) {
    echo '<div class="p-4 bg-green-100 text-green-700 rounded mb-6">✅ No completely orphaned main pages detected (based on simple text analysis).</div>';
} else {
    echo '<div class="overflow-x-auto mb-8"><table class="min-w-full bg-white border border-gray-200">';
    echo '<thead class="bg-gray-50"><tr><th class="py-2 px-4 border-b text-left">Type</th><th class="py-2 px-4 border-b text-left">Page Name</th><th class="py-2 px-4 border-b text-left">Action</th></tr></thead><tbody>';
    foreach ($orphans as $orphan) {
        echo '<tr>';
        echo '<td class="py-2 px-4 border-b">' . $orphan['type'] . '</td>';
        echo '<td class="py-2 px-4 border-b font-medium">' . $orphan['name'] . '</td>';
        echo '<td class="py-2 px-4 border-b"><a href="' . $orphan['url'] . '" class="text-blue-500 hover:underline" target="_blank">View Page</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

// 2. Hub & Spoke Visualization (Simple)
echo '<h2 class="text-xl font-bold mb-4 border-b pb-2 mt-8">🕸️ Hub & Spoke Structure (Destinations -> Packages)</h2>';

foreach ($destinations as $dest) {
    // Get packages for this destination
    $relatedPackages = $db->fetchAll("SELECT title, slug, id FROM packages WHERE destination_id = ?", [$dest['id']]);

    if (!empty($relatedPackages)) {
        echo '<div class="mb-6 p-4 border rounded hover:shadow-md transition">';
        echo '<h3 class="text-lg font-bold text-gray-800 mb-2">🏝️ Hub: ' . htmlspecialchars($dest['name']) . '</h3>';
        echo '<ul class="list-disc pl-6 text-gray-600">';
        foreach ($relatedPackages as $rp) {
            echo '<li><span class="text-indigo-600">Spoke:</span> <a href="' . package_url($rp['slug']) . '" target="_blank" class="hover:underline">' . htmlspecialchars($rp['title']) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

echo '</div></body></html>';
?>