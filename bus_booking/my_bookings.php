<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}
// Handle cancellation
if (isset($_GET['cancel_id'])) {
    $booking_id = $_GET['cancel_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
}
// Fetch user's bookings
$stmt = $conn->prepare("SELECT b.id, bs.bus_name, s.origin, s.destination, s.departure_time, b.num_seats, b.total_price, b.status FROM bookings b JOIN schedules s ON b.schedule_id = s.id JOIN buses bs ON s.bus_id = bs.id WHERE b.user_id = ? ORDER BY s.departure_time DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>My Bookings</h3>
<table border="1">
    <tr><th>Bus</th><th>Route</th><th>Departure</th><th>Seats</th><th>Total Price</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
        <td><?php echo htmlspecialchars($row['origin']) . " to " . htmlspecialchars($row['destination']); ?></td>
        <td><?php echo $row['departure_time']; ?></td>
        <td><?php echo $row['num_seats']; ?></td>
        <td>$<?php echo $row['total_price']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if ($row['status'] == 'Confirmed'): ?>
            <a href="my_bookings.php?cancel_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
            <?php else: echo 'N/A'; endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>