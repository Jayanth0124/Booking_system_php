<?php
include 'db_connect.php';
$origin = $_GET['origin'];
$destination = $_GET['destination'];
$journey_date = $_GET['journey_date'];

$sql = "SELECT t.train_name, t.train_number, r.id as route_id, r.departure_time, r.arrival_time, sa.id as availability_id, sa.total_seats, sa.booked_seats, c.class_name, c.id as class_id, f.fare
        FROM routes r
        JOIN trains t ON r.train_id = t.id
        JOIN seat_availability sa ON r.id = sa.route_id
        JOIN classes c ON sa.class_id = c.id
        JOIN fares f ON r.id = f.route_id AND c.id = f.class_id
        WHERE r.origin_station = ? AND r.destination_station = ? AND sa.journey_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $origin, $destination, $journey_date);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>Available Trains on <?php echo htmlspecialchars($journey_date); ?></h3>
<table border="1">
    <tr><th>Train</th><th>Departs</th><th>Arrives</th><th>Class</th><th>Fare</th><th>Available Seats</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $available = $row['total_seats'] - $row['booked_seats']; ?>
        <?php if ($available > 0): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['train_name']) . " (" . htmlspecialchars($row['train_number']) . ")"; ?></td>
            <td><?php echo date('g:i A', strtotime($row['departure_time'])); ?></td>
            <td><?php echo date('g:i A', strtotime($row['arrival_time'])); ?></td>
            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['fare']); ?></td>
            <td><?php echo $available; ?></td>
            <td>
                <a href="booking.php?route_id=<?php echo $row['route_id']; ?>&class_id=<?php echo $row['class_id']; ?>&date=<?php echo $journey_date; ?>">Book Now</a>
            </td>
        </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>