<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$bookings = $conn->query("SELECT b.*, h.hotel_name FROM bookings b JOIN hotels h ON b.hotel_id = h.id WHERE b.user_id = ".$_SESSION['user_id']." ORDER BY b.check_in_date DESC");
?>
<h3>My Accommodation Bookings</h3>
<table border="1">
    <tr><th>Hotel</th><th>Check-in</th><th>Check-out</th><th>Guests</th><th>Total Price</th><th>Status</th></tr>
    <?php while ($row = $bookings->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
        <td><?php echo $row['check_in_date']; ?></td>
        <td><?php echo $row['check_out_date']; ?></td>
        <td><?php echo $row['num_guests']; ?></td>
        <td>₹<?php echo $row['total_price']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>