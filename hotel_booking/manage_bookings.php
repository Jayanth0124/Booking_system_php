<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit();
}

// Admin can cancel any booking at any time
if (isset($_GET['cancel_id'])) {
    $booking_id = $_GET['cancel_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
}

$result = $conn->query("SELECT b.id, u.username, r.room_type, b.check_in, b.check_out, b.status FROM bookings b JOIN users u ON b.user_id = u.id JOIN rooms r ON b.room_id = r.id ORDER BY b.check_in DESC");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>All Bookings</h3>
<table border="1">
    <tr><th>ID</th><th>User</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
        <td><?php echo $row['check_in']; ?></td>
        <td><?php echo $row['check_out']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if ($row['status'] == 'Confirmed'): ?>
            <a href="manage_bookings.php?cancel_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel Booking</a>
            <?php else: echo 'N/A'; endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>