<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }
$hospital_id = $_SESSION['related_id'];
// Fetch appointments for doctors at this hospital
$stmt = $conn->prepare("
    SELECT a.id, a.appointment_time, a.status, p.full_name as patient_name, d.doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE d.hospital_id = ?
    ORDER BY a.appointment_time DESC
");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>View Appointments</title></head>
<body>
    <a href="hospital_dashboard.php">Back to Dashboard</a>
    <h2>Your Hospital's Appointments</h2>
    <table border="1">
        <tr><th>Patient Name</th><th>Doctor</th><th>Time</th><th>Status</th></tr>
        <?php while ($row = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo $row['appointment_time']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>