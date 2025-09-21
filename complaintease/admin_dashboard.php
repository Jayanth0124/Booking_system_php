<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="manage_officers.php">Manage Officers (Generate ID/Password)</a></li>
    <li><a href="manage_users.php">View/Manage Users</a></li>
    <li><a href="manage_complaints.php">View & Assign Complaints</a></li>
</ul>
<a href="logout.php">Logout</a>