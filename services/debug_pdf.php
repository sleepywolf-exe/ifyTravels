<?php
// services/debug_pdf.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/libs/fpdf.php';

try {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Hello World - PDF Test');
    $pdf->Output();
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
?>