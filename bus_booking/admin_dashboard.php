<?php
include 'db_connect.php';
// Check if the user is an admin
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
        <li><a href="manage_buses.php">Manage Buses & Schedules</a></li>
        <li><a href="manage_categories.php">Manage Bus Categories</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_news.php">Manage News</a></li>
        <li><a href="reports.php">Generate Reports</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>