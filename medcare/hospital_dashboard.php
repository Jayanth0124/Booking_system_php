<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: index.php"); exit(); }
?>
<h1>Hospital Dashboard</h1>
<p>Manage your hospital's information and appointments.</p>
<ul>
    <li><a href="hospital_manage_details.php">Update Hospital Details & Location</a></li>
    <li><a href="hospital_manage_doctors.php">Manage Doctors</a></li>
    <li><a href="hospital_manage_schedules.php">Manage Doctor Schedules</a></li>
    <li><a href="hospital_view_appointments.php">View Your Appointments</a></li>
</ul>
<a href="logout.php">Logout</a>