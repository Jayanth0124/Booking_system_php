<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'cake_shop_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// For simplicity, using plain text passwords. For production, always use password_hash().
?>