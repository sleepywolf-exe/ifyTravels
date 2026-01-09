<?php
// services/pdf_test_minimal.php
// Minimal Test for FPDF

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Define paths
define('FPDF_FONTPATH', __DIR__ . '/../includes/libs/font/');
$fpdfpath = __DIR__ . '/../includes/libs/fpdf.php';

// 2. Check if files exist
if (!file_exists($fpdfpath)) {
    die("Error: FPDF not found at $fpdfpath");
}
if (!is_dir(FPDF_FONTPATH)) {
    die("Error: Font directory not found at " . FPDF_FONTPATH);
}

// 3. Include Library
require($fpdfpath);

// 4. Generate PDF
ob_start();
try {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Cell(40, 10, 'Hello World! PDF Test Successful.');

    // Clear buffer
    ob_end_clean();

    // Output
    $pdf->Output('I', 'test.pdf');

} catch (Exception $e) {
    ob_end_clean();
    echo "PDF Generation Error: " . $e->getMessage();
}
?>