<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'train_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// For simplicity, we are using plain text passwords. For production, use password_hash().
?>