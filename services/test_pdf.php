<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting PDF Test...<br>";

$fpdfPath = '../includes/libs/fpdf.php';
if (!file_exists($fpdfPath)) {
    die("File not found: $fpdfPath");
} else {
    echo "File found: $fpdfPath<br>";
}

require_once $fpdfPath;

echo "Included FPDF.<br>";

if (class_exists('FPDF')) {
    echo "Class FPDF exists.<br>";
} else {
    die("Class FPDF NOT found.");
}

try {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Hello World!');
    echo "PDF Generated object successfully. Output skipped to avoid binary mess.<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
?>