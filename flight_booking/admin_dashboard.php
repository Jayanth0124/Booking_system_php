<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>
    <ul>
        <li><a href="manage_flights.php">Manage Flights & Schedules</a></li>
        <li><a href="manage_inventory.php">Manage Seat Inventory</a></li>
        <li><a href="admin_report.php">View Booking Report</a></li>
        <li><a href="manage_cancellations.php">Manage Cancellations</a></li>
        <li><a href="view_feedback.php">View Customer Feedback</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>