<?php
include 'db_connect.php';

// Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>AgriMart Admin Dashboard</h1>
    <p>Welcome, Admin! From here you can manage the entire platform.</p>

    <h3>Management Menu</h3>
    <ul>
        <li><a href="approve_farmers.php">Approve New Farmers</a></li>
        <li><a href="manage_categories.php">Manage Product Categories</a></li>
        <li><a href="view_reports.php">View Sales & User Reports</a></li>
    </ul>

    <hr>
    <a href="logout.php">Logout</a>
</body>
</html>