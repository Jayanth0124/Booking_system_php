<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('Asia/Kolkata');
$conn = new mysqli('localhost', 'root', '', 'waste_management_db');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
?>