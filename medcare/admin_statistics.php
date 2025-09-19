<?php
include 'db_connect.php';
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$total_patients = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$total_hospitals = $conn->query("SELECT COUNT(*) as count FROM hospitals")->fetch_assoc()['count'];
$approved_hospitals = $conn->query("SELECT COUNT(*) as count FROM hospitals WHERE is_approved = 1")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
$appointment_statuses = $conn->query("SELECT status, COUNT(*) as count FROM appointments GROUP BY status");
?>
<!DOCTYPE html>
<html>
<head><title>System Statistics</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>System Statistics</h2>
    
    <h3>User & Provider Stats</h3>
    <ul>
        <li>Total Registered Patients: <b><?php echo $total_patients; ?></b></li>
        <li>Total Registered Hospitals: <b><?php echo $total_hospitals; ?></b></li>
        <li>Total Approved Hospitals: <b><?php echo $approved_hospitals; ?></b></li>
    </ul>

    <h3>Appointment Stats</h3>
    <ul>
        <li>Total Appointments Booked: <b><?php echo $total_appointments; ?></b></li>
        <?php while ($row = $appointment_statuses->fetch_assoc()): ?>
            <li>Total <?php echo htmlspecialchars($row['status']); ?> Appointments: <b><?php echo $row['count']; ?></b></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>