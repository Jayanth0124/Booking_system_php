<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: login.php"); exit(); }
?>
<h1>Applicant Dashboard</h1>
<p>Welcome, Applicant!</p>
<ul>
    <li><a href="my_profile.php">My Profile & Resume</a></li>
    <li><a href="my_applications.php">My Applications</a></li>
    <li><a href="index.php">Search for New Jobs</a></li>
</ul>
<a href="logout.php">Logout</a>