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
        <li><a href="manage_restaurants.php">Manage Restaurants</a></li>
        <li><a href="manage_tables.php">Manage Table Types & Availability</a></li>
        <li><a href="manage_offers.php">Manage Special Offers</a></li>
        <li><a href="view_reservations.php">View All Customer Reservations</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>