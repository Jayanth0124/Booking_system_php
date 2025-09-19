<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bus_booking_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Security Warning: For simplicity, we are using plain text passwords.
// For a real website, always use password_hash() and password_verify().
?>