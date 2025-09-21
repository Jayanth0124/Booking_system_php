<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="approve_directors.php">Approve New Directors</a></li>
    <li><a href="view_users.php">View All Users (Job Seekers)</a></li>
    <li><a href="admin_report.php">View Report</a></li>
</ul>
<a href="logout.php">Logout</a>