<?php
// Start the session to manage user login state
session_start();

// Database configuration
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'hotel_db';

// Create a connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Security Warning: Storing plain text passwords is not secure.
// This is done for simplicity as requested. For a real application,
// always use password_hash() and password_verify().
?>