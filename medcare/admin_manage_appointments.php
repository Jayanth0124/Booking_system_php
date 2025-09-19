<?php
include 'db_connect.php';
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle cancelling an appointment
if (isset($_GET['cancel_id'])) {
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $_GET['cancel_id']);
    $stmt->execute();
    header("Location: admin_manage_appointments.php");
    exit();
}

// Fetch all appointments with details
$appointments = $conn->query("
    SELECT a.id, a.appointment_time, a.status, p.full_name as patient_name, d.doctor_name, h.hospital_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    JOIN hospitals h ON d.hospital_id = h.id
    ORDER BY a.appointment_time DESC
");
?>
<!DOCTYPE html>
<html>
<head><title>Manage All Appointments</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>All System Appointments</h2>
    <table border="1">
        <tr><th>Patient</th><th>Doctor</th><th>Hospital</th><th>Time</th><th>Status</th><th>Action</th></tr>
        <?php while ($row = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
            <td><?php echo $row['appointment_time']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'Scheduled'): ?>
                <a href="?cancel_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
                <?php else: echo 'N/A'; endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>