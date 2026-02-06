<?php
http_response_code(503);
$errorCode = 503;
$errorTitle = "Service Unavailable";
$errorMessage = "We are currently experiencing technical issues. Please try again later.";
require __DIR__ . '/error.php';
?>