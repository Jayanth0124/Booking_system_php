<?php
include 'db_connect.php';
// Check if the user is a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}
// Fetch patient's name
$stmt = $conn->prepare("SELECT full_name FROM patients WHERE id = ?");
$stmt->bind_param("i", $_SESSION['related_id']);
$stmt->execute();
$patient_name = $stmt->get_result()->fetch_assoc()['full_name'];
?>
<!DOCTYPE html>
<html>
<head><title>Patient Dashboard</title></head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($patient_name); ?>!</h1>
    <p>This is your personal dashboard. From here you can manage your appointments and personal details.</p>
    
    <h3>What would you like to do?</h3>
    <ul>
        <li><a href="index.php">Search for Hospitals & Book a New Appointment</a></li>
        <li><a href="my_appointments.php">View My Appointments (Upcoming & History)</a></li>
        </ul>
    
    <a href="logout.php">Logout</a>
</body>
</html>