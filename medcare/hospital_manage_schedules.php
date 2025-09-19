<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }
$hospital_id = $_SESSION['related_id'];
// Handle adding a schedule
if (isset($_POST['add_schedule'])) {
    $stmt = $conn->prepare("INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $_POST['doctor_id'], $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']);
    $stmt->execute();
}
// Handle deleting a schedule
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
}
// Fetch this hospital's doctors and their schedules
$doctors = $conn->query("SELECT * FROM doctors WHERE hospital_id = $hospital_id");
$schedules = $conn->query("SELECT s.*, d.doctor_name FROM schedules s JOIN doctors d ON s.doctor_id = d.id WHERE d.hospital_id = $hospital_id ORDER BY d.doctor_name, s.day_of_week");
$days = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
?>
<!DOCTYPE html>
<html>
<head><title>Manage Doctor Schedules</title></head>
<body>
    <a href="hospital_dashboard.php">Back to Dashboard</a>
    <h3>Add Doctor Schedule</h3>
    <form method="post">
        Doctor: <select name="doctor_id" required>
            <?php while ($doc = $doctors->fetch_assoc()): ?>
            <option value="<?php echo $doc['id']; ?>"><?php echo htmlspecialchars($doc['doctor_name']); ?></option>
            <?php endwhile; ?>
        </select><br>
        Day of Week: <select name="day_of_week" required>
            <?php foreach($days as $num => $day): ?>
            <option value="<?php echo $num; ?>"><?php echo $day; ?></option>
            <?php endforeach; ?>
        </select><br>
        Start Time: <input type="time" name="start_time" required><br>
        End Time: <input type="time" name="end_time" required><br>
        <button type="submit" name="add_schedule">Add Schedule</button>
    </form>
    <hr>
    <h3>Current Schedules</h3>
    <table border="1">
        <tr><th>Doctor</th><th>Day</th><th>Hours</th><th>Action</th></tr>
        <?php while ($row = $schedules->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo $days[$row['day_of_week']]; ?></td>
            <td><?php echo date('g:i A', strtotime($row['start_time'])) . " - " . date('g:i A', strtotime($row['end_time'])); ?></td>
            <td><a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>