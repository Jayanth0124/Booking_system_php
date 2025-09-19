<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); } 
$result = $conn->query("SELECT b.id, u.username, bs.bus_name, s.origin, s.destination, s.departure_time, b.num_seats, b.total_price, b.status, b.booking_date FROM bookings b JOIN users u ON b.user_id = u.id JOIN schedules s ON b.schedule_id = s.id JOIN buses bs ON s.bus_id = bs.id ORDER BY b.booking_date DESC");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Booking Reports</h3>
<table border="1">
    <tr><th>Booking ID</th><th>User</th><th>Bus</th><th>Route</th><th>Departure</th><th>Seats</th><th>Price</th><th>Status</th><th>Booked On</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
        <td><?php echo htmlspecialchars($row['origin']) . " to " . htmlspecialchars($row['destination']); ?></td>
        <td><?php echo $row['departure_time']; ?></td>
        <td><?php echo $row['num_seats']; ?></td>
        <td>$<?php echo $row['total_price']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['booking_date']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>