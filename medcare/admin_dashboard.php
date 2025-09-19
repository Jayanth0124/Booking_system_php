<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="admin_manage_hospitals.php">Manage Hospitals (Approve/View)</a></li>
    <li><a href="admin_manage_appointments.php">View All Appointments</a></li>
    <li><a href="admin_statistics.php">View Statistics</a></li>
</ul>
<a href="logout.php">Logout</a>