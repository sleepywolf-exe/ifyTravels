<?php
// services/generate_voucher.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/libs/fpdf.php';

// Basic Admin/User Validation (Skip for now to match current implementation, or strictly: if (!is_admin()) ...)
if (!isset($_GET['id'])) {
    die("Booking ID is required.");
}

$booking_id = intval($_GET['id']);
$db = Database::getInstance();
$booking = $db->fetch("SELECT * FROM bookings WHERE id = ?", [$booking_id]);
if (!$booking) {
    die("Booking not found.");
}
$package = $db->fetch("SELECT * FROM packages WHERE id = ?", [$booking['package_id']]);
$settings = $db->fetchAll("SELECT * FROM site_settings");
$config = [];
foreach ($settings as $s) {
    $config[$s['setting_key']] = $s['setting_value'];
}
$siteName = $config['site_name'] ?? 'ifyTravels';

class TicketPDF extends FPDF
{
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }

    function DashedLine($x1, $y1, $x2, $y2, $width = 1, $nb = 15)
    {
        $this->SetLineWidth($width);
        $longueur = abs($x1 - $x2);
        $hauteur = abs($y1 - $y2);
        if ($longueur > $hauteur) {
            $Pointilles = ($longueur / $nb) / 2; // length of dashes
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

// Main Ticket Background (Rounded)
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(200, 200, 200);
$pdf->RoundedRect(5, 5, 200, 90, 5, 'DF');

// Header Block (Left)
$pdf->SetFillColor($teal[0], $teal[1], $teal[2]);
$pdf->RoundedRect(5, 5, 200, 20, 5, 'F');
// Re-draw bottom corners white to "un-round" them if needed, but rounding all 4 is fine for ticket look.
// Actually, let's just draw a teal rect on top half, but we need to respect the rounded corners. 
// Simpler: Just Logo and Text in Teal Header area.

// Header Text
$pdf->SetXY(10, 6);
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(255, 255, 255);
// $pdf->Cell(60, 18, strtoupper($siteName), 0, 0, 'L');
$pdf->Text(12, 18, $siteName);

$pdf->SetFont('Courier', 'B', 14);
$pdf->SetTextColor(255, 255, 255);
$pdf->Text(110, 18, "BOARDING PASS");

$pdf->SetFont('Arial', '', 10);
$pdf->Text(160, 18, "ECONOMY CLASS");

// ---------------------------------------------------------
// Left Body (Main Info)
// ---------------------------------------------------------

$yStart = 35;

// PASSENGER NAME
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 8);
$pdf->Text(12, $yStart, "PASSENGER NAME");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Text(12, $yStart + 6, strtoupper($booking['customer_name']));

// FROM / TO
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 8);
$pdf->Text(12, $yStart + 18, "FROM");
$pdf->Text(60, $yStart + 18, "TO");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Text(12, $yStart + 25, "HOME"); // Or dynamic origin
$pdf->Text(60, $yStart + 25, "DEST"); // Or dynamic dest code

$pdf->SetFont('Arial', '', 10);
$pdf->Text(12, $yStart + 29, "Your Location");
$pdf->Text(60, $yStart + 29, "Destination");

// Package Name (Full)
$pdf->SetXY(12, $yStart + 35);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$pdf->Cell(100, 5, $package['title'] ?? $booking['package_name'], 0, 1);

// DATE / TIME / SEAT
$yRow2 = $yStart + 48;

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 8);
$pdf->Text(12, $yRow2, "DATE");
$pdf->Text(45, $yRow2, "TIME");
$pdf->Text(75, $yRow2, "GATE");
$pdf->Text(100, $yRow2, "SEAT");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Text(12, $yRow2 + 5, date('d M Y', strtotime($booking['travel_date'])));
$pdf->Text(45, $yRow2 + 5, "10:00 AM"); // Dummy
$pdf->Text(75, $yRow2 + 5, "TBD");
$pdf->Text(100, $yRow2 + 5, "1A");


// ---------------------------------------------------------
// Separator (Stub Line)
// ---------------------------------------------------------
$pdf->SetDrawColor(150, 150, 150);
// Dashed Line at X=150
$pdf->DashedLine(150, 5, 150, 95, 0.5, 30);

// ---------------------------------------------------------
// Right Stub (Small Info)
// ---------------------------------------------------------
$pdf->SetXY(155, 35);

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 7);
$pdf->Text(155, 35, "PASSENGER");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Text(155, 40, substr(strtoupper($booking['customer_name']), 0, 18));

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 7);
$pdf->Text(155, 50, "FROM");
$pdf->Text(185, 50, "TO");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Text(155, 55, "HOM");
$pdf->Text(185, 55, "DST");

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Arial', '', 7);
$pdf->Text(155, 65, "DATE");
$pdf->Text(185, 65, "TIME");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Text(155, 70, date('d M', strtotime($booking['travel_date'])));
$pdf->Text(185, 70, "10:00");

// Fake Barcode Area
$pdf->SetFillColor(0, 0, 0);
$pdf->Rect(155, 80, 45, 10, 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Courier', '', 8);
$pdf->Text(160, 86, "TK-" . str_pad($booking['id'], 8, '0', STR_PAD_LEFT));

$pdf->Output('I', 'BoardingPas_' . $booking['id'] . '.pdf');
?>