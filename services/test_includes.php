<?php
// Test if includes are causing issues
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Before includes\n";

require_once __DIR__ . '/../includes/config.php';
echo "After config\n";

require_once __DIR__ . '/../includes/functions.php';
echo "After functions\n";

require_once __DIR__ . '/../includes/libs/fpdf.php';
echo "After fpdf\n";

$output = ob_get_clean();
echo "Buffered output length: " . strlen($output) . "\n";
echo "Content:\n" . htmlspecialchars($output);
?>