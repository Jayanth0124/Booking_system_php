<?php
// This check prevents a "session already active" notice.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set your timezone
date_default_timezone_set('Asia/Kolkata');

// Database connection details
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'blossomgifts_db'; // <-- This is the important line to fix

// Create and check the connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>