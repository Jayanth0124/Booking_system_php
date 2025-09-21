<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'public') { header("Location: login.php"); exit(); }
?>
<h1>Public User Dashboard</h1>
<ul>
    <li><a href="register_complaint.php">Register a New Complaint</a></li>
    <li><a href="my_complaints.php">My Complaints & Status</a></li>
</ul>
<a href="logout.php">Logout</a>