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

// Fetch Site Settings for Logo
$settings = $db->fetchAll("SELECT * FROM site_settings");
$config = [];
foreach ($settings as $s) {
$config[$s['setting_key']] = $s['setting_value'];
}
$siteName = $config['site_name'] ?? 'ifyTravels';
$sitePhone = $config['contact_phone'] ?? '';
$siteEmail = $config['contact_email'] ?? '';

// Create PDF
class PDF extends FPDF
{
public $config;

function Header()
{
global $config;
$logo = '../' . ($config['site_logo'] ?? 'assets/images/logo.png');

if (file_exists($logo)) {
$this->Image($logo, 10, 6, 30);
} else {
// Text fallback
$this->SetFont('Arial', 'B', 24);
$this->Cell(40, 10, $config['site_name'] ?? 'Travels');
}

$this->SetFont('Arial', 'B', 15);
$this->Cell(80);
$this->Cell(100, 10, 'BOOKING VOUCHER', 0, 0, 'R');
$this->Ln(20);

// Company Info
$this->SetFont('Arial', '', 9);
$this->Cell(0, 5, $config['site_name'] ?? 'ifyTravels', 0, 1, 'R');
$this->Cell(0, 5, $config['contact_phone'] ?? '', 0, 1, 'R');
$this->Cell(0, 5, $config['contact_email'] ?? '', 0, 1, 'R');
$this->Ln(10);
$this->Line(10, 45, 200, 45); // Horizontal Line
$this->Ln(10);
}

function Footer()
{
$this->SetY(-15);
$this->SetFont('Arial', 'I', 8);
$this->Cell(0, 10, 'Thank you for booking with us! This is a computer generated invoice.', 0, 0, 'C');
}

function SectionTitle($label)
{
$this->SetFont('Arial', 'B', 12);
$this->SetFillColor(240, 240, 240);
$this->Cell(0, 8, " $label", 0, 1, 'L', true);
$this->Ln(4);
}

function InfoRow($label, $value)
{
$this->SetFont('Arial', 'B', 10);
$this->Cell(50, 6, $label, 0, 0);
$this->SetFont('Arial', '', 10);
$this->Cell(0, 6, $value, 0, 1);
}
}

$pdf = new PDF();
$pdf->config = $config; // Pass config
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Booking Info
$pdf->SectionTitle('Booking Information');
$pdf->InfoRow('Booking Reference:', '#' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT));
$pdf->InfoRow('Date of Booking:', date('F j, Y, g:i a', strtotime($booking['created_at'])));
$pdf->InfoRow('Status:', strtoupper($booking['status']));
$pdf->Ln(5);

// Customer Info
$pdf->SectionTitle('Customer Details');
$pdf->InfoRow('Full Name:', $booking['customer_name']);
$pdf->InfoRow('Email Address:', $booking['customer_email']);
$pdf->InfoRow('Phone Number:', $booking['customer_phone']);
$pdf->Ln(5);

// Travel Details
$pdf->SectionTitle('Travel Details');
$pdf->InfoRow('Package Name:', $package['name'] ?? 'N/A');
$pdf->InfoRow('Travel Date:', date('F j, Y', strtotime($booking['travel_date'])));
$pdf->InfoRow('Number of Travelers:', $booking['travelers']);
$pdf->Ln(5);

// Pricing (Simulated if not in DB, assuming package price * travelers for now)
// In a real app, you might save the exact price at time of booking.
$pricePerPerson = $package['price'] ?? 0;
$totalPrice = $pricePerPerson * ($booking['travelers'] ?? 1);

$pdf->SectionTitle('Payment Summary');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 8, 'Description', 1, 0, 'L');
$pdf->Cell(50, 8, 'Amount', 1, 1, 'R');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 8, ($package['name'] ?? 'Package') . " (" . $booking['travelers'] . " Travelers)", 1, 0, 'L');
$pdf->Cell(50, 8, 'RS ' . number_format($totalPrice), 1, 1, 'R');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, 'Total Value', 1, 0, 'R');
$pdf->Cell(50, 10, 'RS ' . number_format($totalPrice), 1, 1, 'R');

$pdf->Ln(20);

// QR Code Placeholder (Future enhancement)
// $pdf->Image('https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $booking['id'], 160, 200, 30, 0, 'PNG');

$pdf->Output('I', 'Voucher_' . $booking['id'] . '.pdf');
?>