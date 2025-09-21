<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'director') { header("Location: login.php"); exit(); }
?>
<h1>Director Dashboard</h1>
<p>Welcome, Employer!</p>
<ul>
    <li><a href="post_job.php">Post a New Vacancy</a></li>
    <li><a href="my_jobs.php">View Your Posted Vacancies & Applicants</a></li>
</ul>
<a href="logout.php">Logout</a>