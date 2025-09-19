<?php
// This check ensures a session is only started if one isn't already active.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set the default timezone
date_default_timezone_set('Asia/Kolkata');

// Database connection details
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'parlour_db';

// Create the connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// For simplicity, using plain text passwords. For production, always use password_hash().
?>