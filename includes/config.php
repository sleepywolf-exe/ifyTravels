<?php
// includes/config.php

// DB_CONNECTION: 'sqlite' or 'mysql'
// Default to MySQL for Cloud/cPanel deployment. Change to 'sqlite' if testing locally without MySQL.
define('DB_CONNECTION', 'mysql');

// Database Credentails (cPanel / Remote)
// Ignored when using SQLite
define('DB_HOST', 'localhost');
define('DB_NAME', 'bikesraj_ifytravels');
define('DB_USER', 'bikesraj_ifytravels');
define('DB_PASS', 'Secure@123.');

// Facebook Conversions API Configuration
// Replace with actual values from Events Manager
define('FB_PIXEL_ID', '920118194028488');
define('FB_ACCESS_TOKEN', 'EAASGE8rF3JIBQnwcCTDXRbxNB4DgEzZAkW04Lu0txkwJu0NyWwSZC0eZCJvyZCSL2NZAjerJjN9684ZBRUvvtQe5o4iv6t6ZC2VD2oZBdtG4J3pMeSZBGZBjGBsLLIewjgHtY576WKUAz1KWZCg3EK06Vu9U2RVwKxA9tivKPfftJKpRDOW0RoHnqhuBBeLBXqpSAgT8AZDZD');
