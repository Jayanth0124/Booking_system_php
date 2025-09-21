<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$reservations = $conn->query("SELECT r.*, rest.name as restaurant_name FROM reservations r JOIN restaurants rest ON r.restaurant_id = rest.id WHERE r.user_id = ".$_SESSION['user_id']." ORDER BY r.reservation_time DESC");
?>
<h3>My Reservations</h3>
<table border="1">
    <tr><th>Restaurant</th><th>Date & Time</th><th>Guests</th><th>Cost</th><th>Status</th></tr>
    <?php while ($row = $reservations->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
        <td><?php echo $row['reservation_time']; ?></td>
        <td><?php echo $row['num_guests']; ?></td>
        <td>₹<?php echo $row['total_cost']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>