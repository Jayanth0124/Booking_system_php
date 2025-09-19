<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] == 'customer') { header("Location: index.php"); exit(); }
if (isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['appointment_id']);
    $stmt->execute();
}
$appointments = $conn->query("SELECT a.*, s.service_name, st.staff_name, u.username FROM appointments a JOIN services s ON a.service_id = s.id JOIN staff st ON a.staff_id = st.id JOIN users u ON a.customer_id = u.id ORDER BY a.appointment_time DESC");
?>
<h3>All Appointments</h3>
<table border="1">
    <tr><th>Customer</th><th>Service</th><th>Stylist</th><th>Date & Time</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $appointments->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
        <td><?php echo $row['appointment_time']; ?></td>
        <td><b><?php echo $row['status']; ?></b></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                <select name="status">
                    <option value="Confirmed" <?php if($row['status']=='Confirmed') echo 'selected'; ?>>Confirmed</option>
                    <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Cancelled" <?php if($row['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>