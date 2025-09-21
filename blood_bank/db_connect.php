<?php
// This check safely starts a session if one isn't already active.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set the default timezone for consistency.
date_default_timezone_set('Asia/Kolkata');

// --- Database Connection Details ---
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'blood_bank_db'; // Database for this project

// Create and check the connection.
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>