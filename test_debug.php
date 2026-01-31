<?php
require_once 'includes/functions.php';
echo "Base URL (mobile/explore.php): " . base_url('mobile/explore.php') . "\n";
echo "Current Script: " . basename($_SERVER['PHP_SELF']) . "\n";
echo "Are we mobile? " . (isMobileDevice() ? 'Yes' : 'No') . "\n";
?>