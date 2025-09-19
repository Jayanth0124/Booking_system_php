<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
$result = $conn->query("SELECT b.booking_ref, u.username, b.total_fare, b.status, b.booking_date FROM bookings b JOIN users u ON b.user_id = u.id ORDER BY b.booking_date DESC");
?>
<h3>Booking Report</h3>
<table border="1">
    <tr><th>Booking Ref</th><th>User</th><th>Total Fare</th><th>Status</th><th>Date</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['booking_ref']); ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td>₹<?php echo htmlspecialchars($row['total_fare']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td><?php echo $row['booking_date']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>