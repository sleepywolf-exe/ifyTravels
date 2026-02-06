<?php
http_response_code(404);
$errorCode = 404;
$errorTitle = "Look like you're lost";
$errorMessage = "The page you are looking for is not available!";
require __DIR__ . '/error.php';
?>