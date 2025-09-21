<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="approve_hospitals.php">Approve New Hospitals</a></li>
    <li><a href="view_donors.php">View All Donors</a></li>
</ul>
<a href="logout.php">Logout</a>