<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'officer') { header("Location: login.php"); exit(); }
?>
<h1>Officer Dashboard</h1>
<p>Welcome, Officer!</p>
<ul>
    <li><a href="view_assigned_complaints.php">View Assigned Complaints</a></li>
</ul>
<a href="logout.php">Logout</a>