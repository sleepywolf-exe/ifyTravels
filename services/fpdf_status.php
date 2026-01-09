<?php
// services/fpdf_status.php

// 1. Force visual error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>FPDF Diagnostic Tool</h1>";

// 2. Define Paths
$fontPath = __DIR__ . '/../includes/libs/font/';
$fpdfPath = __DIR__ . '/../includes/libs/fpdf.php';

echo "<p><strong>Checking Paths:</strong></p><ul>";
echo "<li>Font Directory: " . $fontPath . " - " . (is_dir($fontPath) ? "<span style='color:green'>FOUND</span>" : "<span style='color:red'>MISSING</span>") . "</li>";
echo "<li>FPDF Library: " . $fpdfPath . " - " . (file_exists($fpdfPath) ? "<span style='color:green'>FOUND</span>" : "<span style='color:red'>MISSING</span>") . "</li>";
echo "</ul>";

// 3. Check Font Access
$helvetica = $fontPath . 'helvetica.php';
echo "<p><strong>Checking Core Font:</strong> " . $helvetica . " - " . (file_exists($helvetica) ? "<span style='color:green'>FOUND</span>" : "<span style='color:red'>MISSING</span>") . "</p>";

// 4. Test Inclusion
echo "<p><strong>Attempting to include FPDF...</strong> ";
try {
    define('FPDF_FONTPATH', $fontPath);
    require($fpdfPath);
    echo "<span style='color:green'>SUCCESS</span></p>";
} catch (Throwable $e) {
    echo "<span style='color:red'>FAILED: " . $e->getMessage() . "</span></p>";
    die();
}

// 5. Test Instantiation
echo "<p><strong>Attempting to instantiate FPDF class...</strong> ";
try {
    $pdf = new FPDF();
    echo "<span style='color:green'>SUCCESS</span></p>";
} catch (Throwable $e) {
    echo "<span style='color:red'>FAILED: " . $e->getMessage() . "</span></p>";
    die();
}

// 6. Test Font Loading
echo "<p><strong>Attempting to load Helvetica font...</strong> ";
try {
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', 'B', 16);
    echo "<span style='color:green'>SUCCESS</span></p>";
} catch (Throwable $e) {
    echo "<span style='color:red'>FAILED: " . $e->getMessage() . "</span></p>";
}

echo "<hr><p style='color:green'><strong>DIAGNOSIS COMPLETE. If you see this, FPDF is working correctly.</strong></p>";
?>