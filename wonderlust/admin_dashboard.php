<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title></head>
<body>
    <h1>Admin Dashboard</h1>
    <ul>
        <li><a href="manage_destinations.php">Post/Manage Tourism Details (Destinations)</a></li>
        <li><a href="manage_hotels.php">Manage Hotel Details</a></li>
        <li><a href="manage_foods.php">Manage Food Details</a></li>
        <li><a href="manage_routes.php">Manage Route Details</a></li>
        <li><a href="view_bookings.php">View All Booking Details</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>