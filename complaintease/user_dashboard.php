<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: login.php"); exit(); }
?>
<h1>User Dashboard</h1>
<p>Welcome, Complainant!</p>
<ul>
    <li><a href="post_complaint.php">Post a New Complaint</a></li>
    <li><a href="my_complaints.php">View My Complaint Status</a></li>
    <li><a href="contact_admin.php">Get Admin Contact Details</a></li>
</ul>
<a href="logout.php">Logout</a>