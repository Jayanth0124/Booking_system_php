<?php
include 'db_connect.php';
$origin = $_GET['origin'];
$destination = $_GET['destination'];
$travel_date = $_GET['travel_date'];

$sql = "SELECT s.id as schedule_id, b.bus_name, b.total_seats, s.departure_time, s.arrival_time, s.price, 
        (SELECT SUM(num_seats) FROM bookings WHERE schedule_id = s.id AND status = 'Confirmed') as booked_seats
        FROM schedules s
        JOIN buses b ON s.bus_id = b.id
        WHERE s.origin LIKE ? AND s.destination LIKE ? AND DATE(s.departure_time) = ?";

$stmt = $conn->prepare($sql);
$like_origin = "%$origin%";
$like_destination = "%$destination%";
$stmt->bind_param("sss", $like_origin, $like_destination, $travel_date);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>Available Buses from <?php echo htmlspecialchars($origin); ?> to <?php echo htmlspecialchars($destination); ?> on <?php echo htmlspecialchars($travel_date); ?></h3>
<table border="1">
    <tr><th>Bus Name</th><th>Departure</th><th>Arrival</th><th>Price</th><th>Available Seats</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $available_seats = $row['total_seats'] - ($row['booked_seats'] ?? 0); ?>
        <?php if ($available_seats > 0): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
            <td><?php echo date('g:i A', strtotime($row['departure_time'])); ?></td>
            <td><?php echo date('g:i A', strtotime($row['arrival_time'])); ?></td>
            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo $available_seats; ?></td>
            <td>
                <form action="booking.php" method="post">
                    <input type="hidden" name="schedule_id" value="<?php echo $row['schedule_id']; ?>">
                    <input type="number" name="num_seats" min="1" max="<?php echo $available_seats; ?>" required>
                    <button type="submit">Book Now</button>
                </form>
            </td>
        </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>