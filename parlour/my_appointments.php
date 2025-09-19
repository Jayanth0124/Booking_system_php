<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
// Fetch user's appointments
$stmt = $conn->prepare("SELECT a.*, s.service_name, st.staff_name FROM appointments a JOIN services s ON a.service_id = s.id JOIN staff st ON a.staff_id = st.id WHERE a.customer_id = ? ORDER BY a.appointment_time DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>My Appointments (Upcoming & History)</h3>
<table border="1">
    <tr><th>Service</th><th>Stylist</th><th>Date & Time</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
        <td><?php echo $row['appointment_time']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if ($row['status'] == 'Confirmed' && strtotime($row['appointment_time']) > time()): ?>
            <a href="cancel_appointment.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
            <?php elseif ($row['status'] == 'Completed'): ?>
            <a href="leave_feedback.php?id=<?php echo $row['id']; ?>">Leave Feedback</a>
            <?php else: echo 'N/A'; endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>