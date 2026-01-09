<?php
// admin/logout.php
require '../includes/functions.php';

// Destroy session completely
$_SESSION = [];
session_destroy();

redirect('login.php');
?>