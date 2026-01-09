<?php
ob_start(); // Start buffer to catch stray output

// Suppress ALL output
error_reporting(0);
ini_set('display_errors', 0);

// Include only what we need
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/libs/fpdf.php';

// Get booking ID
if (!isset($_GET['id'])) {
    ob_end_clean();
    die("Booking ID is required.");
}

$booking_id = intval($_GET['id']);

// Try to get database connection
try {
    $db = Database::getInstance();
    $booking = $db->fetch("SELECT * FROM bookings WHERE id = ?", [$booking_id]);

    if (!$booking) {
        ob_end_clean();
        die("Booking not found.");
    }

    // Handle package data (may be null for general inquiries)
    $package = null;
    if (!empty($booking['package_id'])) {
        $package = $db->fetch("SELECT * FROM packages WHERE id = ?", [$booking['package_id']]);
    }

    $settings = $db->fetchAll("SELECT * FROM site_settings");
    $config = [];
    foreach ($settings as $s) {
        $config[$s['setting_key']] = $s['setting_value'];
    }
    $siteName = $config['site_name'] ?? 'ifyTravels';

} catch (Exception $e) {
    ob_end_clean();
    die("Database error: " . $e->getMessage());
}

class TicketPDF extends FPDF
{
    // Simpler Dashed Line
    function DashedLine($x1, $y1, $x2, $y2, $width = 1, $nb = 15)
    {
        $this->SetLineWidth($width);
        $longueur = abs($x1 - $x2);
        $hauteur = abs($y1 - $y2);
        if ($longueur > $hauteur) {
            $Pointilles = ($longueur / $nb) / 2;
        } else {
            $Pointilles = ($hauteur / $nb) / 2;
        }
        for ($i = $x1; $i <= $x2; $i += $Pointilles + $Pointilles) {
            for ($j = $y1; $j <= $y2; $j += $Pointilles + $Pointilles) {
                if ($longueur > $hauteur) {
                    $this->Line($i, $j, $i + $Pointilles, $j);
                } else {
                    $this->Line($i, $j, $i, $j + $Pointilles);
                }
            }
        }
    }
}

// Create Landscape PDF (Ticket Size)
$pdf = new TicketPDF('L', 'mm', [210, 100]); // Width 210mm, Height 100mm
$pdf->AddPage();

// Colors
$teal = [15, 118, 110];
$dark = [40, 40, 40];
$gray = [100, 100, 100];
$lightGray = [240, 240, 240];

// Main Ticket Background (Standard Rect for stability)
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Rect(5, 5, 200, 90, 'DF');

// Header Block (Left)
$pdf->SetFillColor($teal[0], $teal[1], $teal[2]);
$pdf->Rect(5, 5, 200, 20, 'F');

// Header Text
$pdf->SetXY(10, 6);
$pdf->SetFont('Helvetica', 'B', 24);
$pdf->SetTextColor(255, 255, 255);
$pdf->Text(12, 18, $siteName);

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->SetTextColor(255, 255, 255);
$pdf->Text(110, 18, "BOARDING PASS");

$pdf->SetFont('Helvetica', '', 10);
$pdf->Text(160, 18, "ECONOMY CLASS");

// ---------------------------------------------------------
// Left Body (Main Info)
// ---------------------------------------------------------

$yStart = 35;

// Safe Data Extraction
$customerName = $booking['customer_name'] ?? 'Traveler';
$travelDate = $booking['travel_date'] ?? date('Y-m-d');
$from = 'HOME';
$to = 'DEST';

// PASSENGER NAME
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $yStart, "PASSENGER NAME");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Text(12, $yStart + 6, strtoupper($customerName));

// FROM / TO
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $yStart + 18, "FROM");
$pdf->Text(60, $yStart + 18, "TO");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Text(12, $yStart + 25, $from);
$pdf->Text(60, $yStart + 25, $to);

$pdf->SetFont('Helvetica', '', 10);
$pdf->Text(12, $yStart + 29, "Your Location");
$pdf->Text(60, $yStart + 29, "Destination");

// Package Name (Full)
$pdf->SetXY(12, $yStart + 35);
$pdf->SetFont('Helvetica', 'I', 9);
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$packageName = ($package && !empty($package['title'])) ? $package['title'] : ($booking['package_name'] ?? 'Custom Travel Request');
$pdf->Cell(100, 5, $packageName, 0, 1);

// DATE / TIME / SEAT
$yRow2 = $yStart + 48;

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $yRow2, "DATE");
$pdf->Text(45, $yRow2, "TIME");
$pdf->Text(75, $yRow2, "GATE");
$pdf->Text(100, $yRow2, "SEAT");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->Text(12, $yRow2 + 5, date('d M Y', strtotime($travelDate)));
$pdf->Text(45, $yRow2 + 5, "10:00 AM");
$pdf->Text(75, $yRow2 + 5, "TBD");
$pdf->Text(100, $yRow2 + 5, "1A");


// ---------------------------------------------------------
// Separator (Stub Line)
// ---------------------------------------------------------
$pdf->SetDrawColor(150, 150, 150);
$pdf->DashedLine(150, 5, 150, 95, 0.5, 30);

// ---------------------------------------------------------
// Right Stub (Small Info)
// ---------------------------------------------------------
$pdf->SetXY(155, 35);

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Text(155, 35, "PASSENGER");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text(155, 40, substr(strtoupper($customerName), 0, 18));

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Text(155, 50, "FROM");
$pdf->Text(185, 50, "TO");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Text(155, 55, substr($from, 0, 3));
$pdf->Text(185, 55, substr($to, 0, 3));

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 7);
$pdf->Text(155, 65, "DATE");
$pdf->Text(185, 65, "TIME");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Text(155, 70, date('d M', strtotime($travelDate)));
$pdf->Text(185, 70, "10:00");

// Fake Barcode Area
$pdf->SetFillColor(0, 0, 0);
$pdf->Rect(155, 80, 45, 10, 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Courier', '', 8);
$pdf->Text(160, 86, "TK-" . str_pad($booking['id'], 8, '0', STR_PAD_LEFT));

// Clear any buffered output (whitespace, warnings, etc.)
ob_end_clean();
$pdf->Output('I', 'BoardingPass_' . $booking['id'] . '.pdf');
?>