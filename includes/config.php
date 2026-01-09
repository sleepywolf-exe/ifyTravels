<?php
// includes/config.php

// DB_CONNECTION: 'sqlite' or 'mysql'
// CHANGE THIS TO 'mysql' WHEN UPLOADING TO CPANEL
define('DB_CONNECTION', 'sqlite');

// Database Credentails (cPanel / Remote)
define('DB_HOST', 'localhost');
define('DB_NAME', 'bikesraj_ifytravels');
define('DB_USER', 'bikesraj_ifytravels');
define('DB_PASS', 'Secure@123.');

// SQLite Path (if DB_CONNECTION is 'sqlite')
define('DB_SQLITE_PATH', __DIR__ . '/../db/database.db');
