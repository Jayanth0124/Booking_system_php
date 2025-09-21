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
        <li><a href="manage_drivers.php">Manage Drivers</a></li>
        <li><a href="manage_bins.php">Manage Garbage Bins</a></li>
        <li><a href="manage_routes.php">Manage & Create Routes</a></li>
        <li><a href="assign_routes.php">Assign Daily Routes to Drivers</a></li>
        <li><a href="garbage_report.php">View Garbage Bin Report</a></li>
        <li><a href="view_complaints.php">View Public Complaints</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>