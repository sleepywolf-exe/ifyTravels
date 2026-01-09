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

    // Footer removed to prevent auto-paging conflicts
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


// --- SECTION 2: TRAVEL DETAILS (Clean Layout) ---
$y = 80;
$pdf->SetDrawColor(230, 230, 230);
$pdf->Line(10, $y, 200, $y); // Top Line

// Labels
$pdf->SetXY(10, $y + 5);
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(60, 5, "PACKAGE", 0, 0);
$pdf->Cell(50, 5, "DESTINATION", 0, 0);
$pdf->Cell(40, 5, "TRAVEL DATE", 0, 0);
$pdf->Cell(40, 5, "DURATION", 0, 1); // Added Duration placeholder

// Values
$pdf->SetXY(10, $y + 12);
$pdf->SetFont('Helvetica', '', 11);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);

// Package Name (MultiCell for wrapping)
$x = $pdf->GetX();
$y_curr = $pdf->GetY();
$pdf->MultiCell(55, 5, $packageTitle, 0, 'L');
$y_after_pkg = $pdf->GetY();

// Destination
$pdf->SetXY(70, $y_curr);
$destDisplay = (strtoupper($destinationName) === 'DESTINATION') ? 'International' : $destinationName;
$pdf->Cell(50, 5, $destDisplay, 0, 0);

// Date
$pdf->SetXY(120, $y_curr);
$pdf->Cell(40, 5, date('d F, Y', strtotime($travelDate)), 0, 0);

// Duration (Placeholder from package or default)
$pdf->SetXY(160, $y_curr);
$duration = (!empty($package) && !empty($package['duration'])) ? $package['duration'] : 'Standard Trip';
$pdf->Cell(40, 5, $duration, 0, 0);

// Reset Y to below the lowest element
$pdf->SetY(max($y_after_pkg, $y_curr + 10) + 10);


// --- SECTION 3: CHARGES ---
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(0, 10, "CHARGES BREAKDOWN", 0, 1, 'L');

// Table Header
$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(110, 10, "  Description", 0, 0, 'L', true);
$pdf->Cell(25, 10, "Qty", 0, 0, 'C', true);
$pdf->Cell(30, 10, "Unit Price", 0, 0, 'R', true);
$pdf->Cell(25, 10, "Amount  ", 0, 1, 'R', true);

// Table Rows
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);

// Item: Package
$pdf->Cell(110, 12, "  " . $packageTitle, 'B', 0, 'L');
$pdf->Cell(25, 12, "1", 'B', 0, 'C');
$pdf->Cell(30, 12, number_format($packagePrice, 2), 'B', 0, 'R');
$pdf->Cell(25, 12, number_format($packagePrice, 2) . "  ", 'B', 1, 'R');

// Special Requests (if any) as a line item note
if (!empty($booking['special_requests'])) {
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
    $pdf->Cell(10, 8, "", 0, 0); // Indent
    $pdf->Cell(180, 8, "Note: " . substr($booking['special_requests'], 0, 80) . "...", 0, 1, 'L');
}


// Totals Section
$pdf->Ln(5);
$pdf->SetX(120);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(40, 8, "Subtotal:", 0, 0, 'R');
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(30, 8, number_format($totalAmount, 2), 0, 1, 'R');

$pdf->SetX(120);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->Cell(40, 8, "Tax (0%):", 0, 0, 'R');
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(30, 8, "0.00", 0, 1, 'R');

$pdf->SetX(120);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(130, $pdf->GetY(), 190, $pdf->GetY()); // Separator line for total
$pdf->Ln(2);

$pdf->SetX(120);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->SetTextColor($teal[0], $teal[1], $teal[2]);
$pdf->Cell(40, 10, "Total Due:", 0, 0, 'R');
$pdf->Cell(30, 10, "$" . number_format($totalAmount, 2), 0, 1, 'R');


// --- SECTION 4: FOOTER & TERMS ---
// Disable AutoPageBreak to allow writing to the absolute bottom without triggering a new page
$pdf->SetAutoPageBreak(false);

$y_footer_start = 240;

// Terms & Conditions
$pdf->SetY($y_footer_start);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
$pdf->Cell(0, 6, "Terms & Conditions", 0, 1, 'L');

$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor($gray[0], $gray[1], $gray[2]);
$pdf->MultiCell(0, 4, "1. This voucher must be presented upon arrival at the destination.\n2. Cancellations made within 24 hours of travel differ by package policy.\n3. Please verify visa requirements for international travel.\n4. For emergency assistance or changes, contact support@ifytravels.com.", 0, 'L');

// Divider Line
$pdf->SetY($y_footer_start + 25);
$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

// Thank You Note
$pdf->SetY($y_footer_start + 28);
$pdf->SetFont('Helvetica', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, 'Thank you for booking with ifyTravels. For support, verify your booking at ifytravels.com', 0, 1, 'C');
$pdf->Cell(0, 5, 'This is a computer generated invoice and requires no signature.', 0, 1, 'C');

// Decorative Footer Line (Bottom of A4)
$pdf->SetY(-5);
$pdf->SetFillColor($teal[0], $teal[1], $teal[2]);
$pdf->Rect(0, 292, 210, 5, 'F');

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