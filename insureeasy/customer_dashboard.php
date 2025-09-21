<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') { header("Location: login.php"); exit(); }
?>
<h1>Customer Dashboard</h1>
<p>Welcome, Customer! Here you can view your insurance policy details.</p>
<ul>
    <li><a href="my_policies.php">View My Policy Details</a></li>
</ul>
<a href="logout.php">Logout</a>