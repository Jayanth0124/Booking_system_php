<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] == 'customer') { header("Location: index.php"); exit(); } ?>
<h1>Admin & Staff Dashboard</h1>
<ul>
    <li><a href="manage_appointments.php">Manage All Appointments</a> (Reservations Calendar)</li>
    <li><a href="manage_schedules.php">Manage Staff Schedules & Lockouts</a></li>
    <li><a href="manage_services.php">Manage Services</a></li>
    <li><a href="manage_staff.php">Manage Staff</a></li>
</ul>
<a href="logout.php">Logout</a>