<?php
/**
 * SCMS — Database Configuration & Connection
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'scms_db';

// Connect to database
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Check if connection failed
if (!$conn) {
    echo 'Error: Cannot connect to database';
    exit;
}

// Set character encoding to UTF-8
mysqli_set_charset($conn, 'utf8');

// Include helper functions
require_once __DIR__ . '/functions.php';
