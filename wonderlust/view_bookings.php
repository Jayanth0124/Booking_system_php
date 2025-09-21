<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
$bookings = $conn->query("SELECT b.*, h.hotel_name, u.username FROM bookings b JOIN hotels h ON b.hotel_id = h.id JOIN users u ON b.user_id = u.id ORDER BY b.booking_date DESC");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h2>All Customer Bookings</h2>
<table border="1">
    <tr><th>User</th><th>Hotel</th><th>Check-in</th><th>Check-out</th><th>Price</th><th>Status</th></tr>
    <?php while ($row = $bookings->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
        <td><?php echo $row['check_in_date']; ?></td>
        <td><?php echo $row['check_out_date']; ?></td>
        <td>₹<?php echo $row['total_price']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>