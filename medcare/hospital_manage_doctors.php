<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }

$hospital_id = $_SESSION['related_id'];

// Handle adding a new doctor
if (isset($_POST['add_doctor'])) {
    $stmt = $conn->prepare("INSERT INTO doctors (doctor_name, specialty, hospital_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['doctor_name'], $_POST['specialty'], $hospital_id);
    $stmt->execute();
}
// Handle deleting a doctor
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ? AND hospital_id = ?");
    $stmt->bind_param("ii", $_GET['delete_id'], $hospital_id);
    $stmt->execute();
}
// Fetch this hospital's doctors
$stmt = $conn->prepare("SELECT * FROM doctors WHERE hospital_id = ? ORDER BY doctor_name");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$doctors = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>Manage Doctors</title></head>
<body>
    <a href="hospital_dashboard.php">Back to Dashboard</a>
    <h3>Add a New Doctor</h3>
    <form method="post">
        Doctor Name: <input type="text" name="doctor_name" required><br>
        Specialty: <input type="text" name="specialty" required><br>
        <button type="submit" name="add_doctor">Add Doctor</button>
    </form>
    <hr>
    <h3>Your Doctors</h3>
    <table border="1">
        <tr><th>Name</th><th>Specialty</th><th>Action</th></tr>
        <?php while ($row = $doctors->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['specialty']); ?></td>
            <td><a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>