<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); } ?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="manage_trains.php">Manage Trains & Routes</a></li>
    <li><a href="manage_availability.php">Manage Seat Availability</a></li>
    <li><a href="manage_cancellations.php">Manage Cancellations & Refunds</a></li>
</ul>
<a href="logout.php">Logout</a>