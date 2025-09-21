<?php
// Safely start a session if one isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set your timezone for consistent date/time functions
date_default_timezone_set('Asia/Kolkata');

// --- Database Connection Details ---
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'kodaikanaleats_db'; // The database for this project

// Create and check the connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>