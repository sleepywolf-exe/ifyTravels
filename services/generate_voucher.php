<?php
// services/generate_voucher.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php'; // Includes db.php and starts session

// Basic Admin Check
if (!is_admin()) {
    die("Access Denied. Please log in as admin.");
}
require_once __DIR__ . '/../includes/libs/fpdf.php';

if (!isset($_GET['id'])) {
    die("Booking ID is required.");
}

$booking_id = intval($_GET['id']);
$db = Database::getInstance();

// Fetch Booking Details
$booking = $db->fetch("SELECT * FROM bookings WHERE id = ?", [$booking_id]);
if (!$booking) {
    die("Booking not found.");
}

// Fetch Package Details
$package = $db->fetch("SELECT * FROM packages WHERE id = ?", [$booking['package_id']]);

// Fetch Site Settings
$settings = $db->fetchAll("SELECT * FROM site_settings");
$config = [];
foreach ($settings as $s) {
    $config[$s['setting_key']] = $s['setting_value'];
}

class PDF extends FPDF
{
    public $config;

    // Custom Header
    function Header()
    {
        global $config;

        // Brand Color: Teal (#0F766E -> RGB: 15, 118, 110)
        $this->SetFillColor(15, 118, 110);
        $this->Rect(0, 0, 210, 40, 'F'); // Full width header background

        // Logo Logic
        $logoPath = $config['site_logo'] ?? '';
        $fullLogoPath = __DIR__ . '/../' . $logoPath;

        // Verify if logo exists and is an image
        if ($logoPath && file_exists($fullLogoPath)) {
            // Place Logo (White background box optional, or transparent if PNG)
            // $this->Image($fullLogoPath, 10, 8, 30); 
            // Better positioning
            $this->Image($fullLogoPath, 10, 8, 0, 24); // Height 24mm, auto width
        } else {
            // Fallback: Text Logo
            $this->SetFont('Arial', 'B', 24);
            $this->SetTextColor(255, 255, 255); // White
            $this->SetXY(10, 10);
            $this->Cell(60, 20, $config['site_name'] ?? 'ifyTravels', 0, 0, 'L');
        }

        // Invoice Title
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(110, 10);
        $this->Cell(90, 20, 'BOOKING VOUCHER', 0, 0, 'R');

        // Company Details (White text below title)
        $this->SetFont('Arial', '', 9);
        $this->SetXY(110, 22);
        $this->Cell(90, 5, $config['contact_email'] ?? '', 0, 1, 'R');
        $this->SetX(110);
        $this->Cell(90, 5, $config['contact_phone'] ?? '', 0, 1, 'R');

        $this->Ln(25); // Move cursor down after header
    }

    // Custom Footer
    function Footer()
    {
        $this->SetY(-30);

        // Teal Line
        $this->SetDrawColor(15, 118, 110);
        $this->SetLineWidth(1);
        $this->Line(10, $this->GetY(), 200, $this->GetY());

        // Thank you note
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Thank you for choosing us for your journey!', 0, 1, 'C');

        // Legal/System text
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 5, 'This is a computer generated document and does not require a signature.', 0, 1, 'C');
        $this->Cell(0, 5, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionHeader($title)
    {
        $this->Ln(8);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(15, 118, 110); // Teal
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L');
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY()); // Underline
        $this->Ln(4);
    }

    function KeyValueRow($key, $value)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(50, 7, $key, 0, 0);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 7, $value, 0, 1);
    }
}

// Instantiate PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->config = $config;
$pdf->AddPage();

// 1. Booking Information
$pdf->SectionHeader('Booking Information');
$pdf->KeyValueRow('Booking Reference:', '#' . str_pad((string) $booking['id'], 6, '0', STR_PAD_LEFT));
$pdf->KeyValueRow('Date of Booking:', date('F j, Y, g:i a', strtotime($booking['created_at'])));
$pdf->KeyValueRow('Status:', strtoupper($booking['status']));

// 2. Customer Details
$pdf->SectionHeader('Customer Details');
$pdf->KeyValueRow('Full Name:', $booking['customer_name'] ?? 'N/A');
$pdf->KeyValueRow('Email Address:', $booking['email'] ?? 'N/A');
$pdf->KeyValueRow('Phone Number:', $booking['phone'] ?? 'N/A');

// 3. Travel Details
$pdf->SectionHeader('Travel Details');
$pdf->KeyValueRow('Package Name:', $package['title'] ?? ($booking['package_name'] ?? 'N/A')); // Use Title from packages, fallback to booking snapshot
$pdf->KeyValueRow('Travel Date:', date('F j, Y', strtotime($booking['travel_date'])));
$travelers = $booking['travelers'] ?? 1;
$pdf->KeyValueRow('Number of Travelers:', $travelers);

// 4. Payment Summary (Styled Table)
$pdf->Ln(10);
$pdf->SetFillColor(240, 245, 245); // Light Teal/Gray
$pdf->SetTextColor(15, 118, 110); // Teal Text
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(140, 10, 'Description', 1, 0, 'L', true);
$pdf->Cell(50, 10, 'Amount', 1, 1, 'R', true);

// Row 1
$pricePerPerson = $package['price'] ?? ($booking['total_price'] / max($travelers, 1)); // Heuristic if price not saved
$totalPrice = $booking['total_price'] > 0 ? $booking['total_price'] : ($pricePerPerson * $travelers);

$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(140, 10, ($package['title'] ?? 'Package') . " (x$travelers Travelers)", 1, 0, 'L');
$pdf->Cell(50, 10, 'Rs. ' . number_format($totalPrice), 1, 1, 'R');

// Total Row
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(15, 118, 110);
$pdf->Cell(140, 12, 'Total Value', 1, 0, 'R');
$pdf->Cell(50, 12, 'Rs. ' . number_format($totalPrice), 1, 1, 'R');

// Output
$pdf->Output('I', 'Voucher_' . $booking['id'] . '.pdf');
?>