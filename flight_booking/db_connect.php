<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'flight_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// For simplicity, plain text passwords are used. For production, use password_hash().
?>