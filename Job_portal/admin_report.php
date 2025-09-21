<?php
include 'db_connect.php';
// Ensure the user is an admin.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch various statistics using COUNT queries.
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_directors = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'director'")->fetch_assoc()['count'];
$approved_directors = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'director' AND is_approved = 1")->fetch_assoc()['count'];
$total_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head><title>Admin Report</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Site Report & Statistics</h2>
    
    <h3>User & Director Stats</h3>
    <ul>
        <li>Total Registered Job Seekers: <b><?php echo $total_users; ?></b></li>
        <li>Total Registered Directors: <b><?php echo $total_directors; ?></b></li>
        <li>Total Approved Directors: <b><?php echo $approved_directors; ?></b></li>
    </ul>

    <h3>Activity Stats</h3>
    <ul>
        <li>Total Jobs Posted: <b><?php echo $total_jobs; ?></b></li>
        <li>Total Applications Made: <b><?php echo $total_applications; ?></b></li>
    </ul>
</body>
</html>