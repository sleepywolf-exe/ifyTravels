<?php
// services/generate_voucher.php
// COMPLETE REWRITE - Self-contained to avoid dependencies

// 1. CLEAR BUFFERS IMMEDIATELY
while (ob_get_level())
    ob_end_clean();
ob_start();

// 2. ERROR HANDLING (Log to file, don't show to browser)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 3. DEFINE PATHS
$rootPath = __DIR__ . '/../';
define('FPDF_FONTPATH', $rootPath . 'includes/libs/font/');
require($rootPath . 'includes/libs/fpdf.php');
require($rootPath . 'includes/config.php');

// 4. DATA FETCHING (Scanning inputs)
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($booking_id === 0)
    die("Invalid Booking ID");

// 5. MANUAL DB CONNECTION (Isolate from db.php issues)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch Booking
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking)
        die("Booking Not Found");

    // Fetch Package & Destination
    $package = null;
    $destinationName = "DESTINATION";

    if (!empty($booking['package_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt->execute([$booking['package_id']]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($package && !empty($package['destination_id'])) {
            $stmt = $pdo->prepare("SELECT name FROM destinations WHERE id = ?");
            $stmt->execute([$package['destination_id']]);
            $dest = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dest)
                $destinationName = strtoupper($dest['name']);
        }
    }
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// 6. PREPARE DATA VARS
$customerName = $booking['customer_name'] ?? 'Traveler';
$packageName = (!empty($package) && !empty($package['title'])) ? $package['title'] : ($booking['package_name'] ?? 'Custom Trip');
$travelDate = $booking['travel_date'] ?? date('Y-m-d');
$bookingRef = "TK-" . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
$status = strtoupper($booking['status'] ?? 'PENDING');
$amount = number_format($booking['total_amount'] ?? 0, 2);

// 7. GENERATE PDF
class BoardingPass extends FPDF
{
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

// Init PDF in Landscape
$pdf = new BoardingPass('L', 'mm', [210, 100]);
$pdf->AddPage();

// -- DESIGN START --

// Colors
$teal = [15, 118, 110];
$dark = [30, 30, 30];
$gray = [100, 100, 100];
$red = [200, 50, 50];

// Main Border
$pdf->SetDrawColor(200, 200, 200);
$pdf->Rect(5, 5, 200, 90);

// Header Bar
$pdf->SetFillColor($teal[0], $teal[1], $teal[2]);
$pdf->Rect(5, 5, 200, 18, 'F');

// Header Text
$pdf->SetXY(10, 5);
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(80, 18, 'ifyTravels', 0, 0, 'L');

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(110, 18, 'BOARDING PASS', 0, 0, 'R');

// Content Area
$y = 30;

// Row 1: Passenger
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $y, "PASSENGER NAME");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Text(12, $y + 6, strtoupper(substr($customerName, 0, 25)));

// Status Badge (Right aligned in main section)
$pdf->SetXY(110, $y);
$pdf->SetFont('Helvetica', 'B', 10);
if ($status === 'CONFIRMED') {
    $pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
} elseif ($status === 'CANCELLED') {
    $pdf->SetTextColor($red[0], $red[1], $red[2]);
} else {
    $pdf->SetTextColor(200, 150, 0); // Orange for Pending
}
$pdf->Cell(30, 6, "STATUS: " . $status, 0, 0, 'R');

// Row 2: Route
$y += 15;
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $y, "FROM");
$pdf->Text(60, $y, "TO");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Text(12, $y + 7, "HOME"); // Usually user's location, static for now
$pdf->Text(60, $y + 7, substr($destinationName, 0, 12));

// Row 3: Package Name
$y += 14;
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$pdf->SetFont('Helvetica', 'I', 10);
$pdf->Text(12, $y, $packageName);

// Row 4: Details (Date, Time, Price)
$y += 10;
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text(12, $y, "DATE");
$pdf->Text(45, $y, "TIME");
$pdf->Text(75, $y, "PRICE");
$pdf->Text(105, $y, "SEAT");

$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->Text(12, $y + 5, date('d M Y', strtotime($travelDate)));
$pdf->Text(45, $y + 5, "10:00 AM");
$pdf->Text(75, $y + 5, "$" . $amount);
$pdf->Text(105, $y + 5, "1A");

// Dashed Separator
$pdf->SetDrawColor(150, 150, 150);
$pdf->DashedLine(150, 5, 150, 95);

// Stub (Right Side)
$stubX = 155;
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Text($stubX, 35, "PASSENGER");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text($stubX, 40, substr(strtoupper($customerName), 0, 15));

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Text($stubX, 50, "DESTINATION");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Text($stubX, 55, substr($destinationName, 0, 3));

$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Text($stubX, 65, "DATE");
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text($stubX, 70, date('d M', strtotime($travelDate)));

// Fake Zip/Barcode
$pdf->SetFillColor(0, 0, 0);
$pdf->Rect($stubX, 75, 40, 10, 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Text($stubX + 2, 81, $bookingRef);

// -- OUTPUT --

// Clean buffer one last time
if (ob_get_length())
    ob_end_clean();

// Force Headers
header('Content-Type: application/pdf');
header('Cache-Control: no-cache, must-revalidate');

$pdf->Output('I', 'Voucher_' . $booking_id . '.pdf');
exit;
?>