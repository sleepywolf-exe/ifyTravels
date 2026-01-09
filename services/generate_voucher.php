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
$customerEmail = $booking['email'] ?? 'N/A';
$customerPhone = $booking['phone'] ?? 'N/A';
$packageTitle = (!empty($package) && !empty($package['title'])) ? $package['title'] : ($booking['package_name'] ?? 'Custom Trip');
$packagePrice = (!empty($package) && !empty($package['price'])) ? $package['price'] : 0;
// If booking has a total_amount, use it, otherwise fallback to package price
$totalAmount = (!empty($booking['total_amount']) && $booking['total_amount'] > 0) ? $booking['total_amount'] : $packagePrice;

$travelDate = $booking['travel_date'] ?? date('Y-m-d');
$createdDate = isset($booking['created_at']) ? date('M d, Y', strtotime($booking['created_at'])) : date('M d, Y');
$bookingRef = "INV-" . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
$status = strtoupper($booking['status'] ?? 'PENDING');

// 7. GENERATE PDF (Professional A4 Invoice)
class InvoicePDF extends FPDF
{
    function Header()
    {
        // Logo or Company Name
        $this->SetFillColor(15, 118, 110); // Teal
        $this->Rect(0, 0, 210, 20, 'F');

        $this->SetXY(10, 5);
        $this->SetFont('Helvetica', 'B', 20);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(100, 10, 'ifyTravels', 0, 0, 'L');

        $this->SetXY(150, 5);
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(50, 10, 'TRAVEL INVOICE', 0, 0, 'R');
    }

    function Footer()
    {
        $this->SetY(-30);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Thank you for booking with ifyTravels. For support, verify your booking at ifytravels.com', 0, 1, 'C');
        $this->Cell(0, 5, 'This is a computer generated invoice and requires no signature.', 0, 0, 'C');
    }
}

// Init PDF in Portrait A4
$pdf = new InvoicePDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 35);

// Colors
$teal = [15, 118, 110];
$dark = [33, 33, 33];
$gray = [100, 100, 100];
$lightGray = [240, 240, 240];

// --- SECTION 1: INVOICE META & BILLING ---
$y = 35;

// Left Column: Invoice To
$pdf->SetXY(10, $y);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(50, 6, "BILLED TO:", 0, 1);

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(50, 6, strtoupper($customerName), 0, 1);

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(50, 5, $customerEmail, 0, 1);
$pdf->Cell(50, 5, $customerPhone, 0, 1);

// Right Column: Details
$pdf->SetXY(120, $y);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(80, 6, "INVOICE DETAILS:", 0, 1, 'R');

$pdf->SetX(120);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(40, 6, "Invoice No:", 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(40, 6, $bookingRef, 0, 1, 'R');

$pdf->SetX(120);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(40, 6, "Date Issued:", 0, 0, 'R');
$pdf->Cell(40, 6, $createdDate, 0, 1, 'R');

$pdf->SetX(120);
$pdf->Cell(40, 6, "Status:", 0, 0, 'R');

// Status Color
if ($status === 'CONFIRMED')
    $pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
elseif ($status === 'CANCELLED')
    $pdf->SetTextColor(200, 50, 50);
else
    $pdf->SetTextColor(200, 150, 0);

$pdf->Cell(40, 6, $status, 0, 1, 'R');


// --- SECTION 2: TRAVEL SUMMARY (Visual Box) ---
$y = 75;
$pdf->SetFillColor($lightGray[0], $lightGray[1], $lightGray[2]);
$pdf->Rect(10, $y, 190, 30, 'F');

$pdf->SetXY(15, $y + 5);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(40, 5, "DESTINATION", 0, 0);
$pdf->Cell(50, 5, "PACKAGE", 0, 0);
$pdf->Cell(50, 5, "TRAVEL DATE", 0, 0);

$pdf->SetXY(15, $y + 12);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$pdf->Cell(40, 8, is_array($destinationName) ? substr($destinationName['name'], 0, 15) : strtoupper(substr($destinationName, 0, 15)), 0, 0);
$pdf->Cell(50, 8, substr($packageTitle, 0, 25) . '...', 0, 0);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(50, 8, date('d M, Y', strtotime($travelDate)), 0, 0);


// --- SECTION 3: ITEM TABLE ---
$y = 120;

// Table Header
$pdf->SetXY(10, $y);
$pdf->SetFillColor($dark[0], $dark[1], $dark[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(100, 10, "  Description", 0, 0, 'L', true);
$pdf->Cell(30, 10, "Quantity", 0, 0, 'C', true);
$pdf->Cell(30, 10, "Price", 0, 0, 'R', true);
$pdf->Cell(30, 10, "Total  ", 0, 1, 'R', true);

// Table Rows
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', '', 10);

// Item 1: Package Cost
$pdf->Cell(100, 12, "  " . $packageTitle, 'B', 0, 'L');
$pdf->Cell(30, 12, "1", 'B', 0, 'C');
$pdf->Cell(30, 12, number_format($packagePrice, 2), 'B', 0, 'R');
$pdf->Cell(30, 12, number_format($packagePrice, 2) . "  ", 'B', 1, 'R');

// Item 2: Taxes/Fees (Placeholder)
// $pdf->Cell(100, 12, "  Processing Fees & Taxes", 'B', 0, 'L');
// $pdf->Cell(30, 12, "1", 'B', 0, 'C');
// $pdf->Cell(30, 12, "0.00", 'B', 0, 'R');
// $pdf->Cell(30, 12, "0.00  ", 'B', 1, 'R');

// Totals Section
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(160, 15, "TOTAL AMOUNT  ", 0, 0, 'R');
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$pdf->Cell(30, 15, "$" . number_format($totalAmount, 2) . "  ", 0, 1, 'R');

// Payment Status
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(160, 8, "Payment Status:  ", 0, 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(30, 8, ($status == 'CONFIRMED' ? 'Paid' : 'Due on Arrival') . "  ", 0, 1, 'R');


// --- SECTION 4: TERMS & BRANDING ---
$pdf->SetY(220);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(0, 8, "Terms & Conditions", 0, 1, 'L');

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->MultiCell(0, 5, "1. This voucher must be presented upon arrival.\n2. Cancellations made within 24 hours of travel differ by package policy.\n3. Please verify visa requirements for international travel.\n4. For emergency assistance, contact support@ifytravels.com.", 0, 'L');

// -- OUTPUT --

// Clean buffer one last time
if (ob_get_length())
    ob_end_clean();

// Force Headers
header('Content-Type: application/pdf');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output('I', 'Invoice_' . $booking_id . '.pdf');
exit;
?>