<?php
include 'db_connect.php';
$origin = $_GET['origin'];
$destination = $_GET['destination'];
$flight_date = $_GET['flight_date'];

$sql = "SELECT s.id as schedule_id, a.airline_name, f.flight_number, s.departure_time, s.arrival_time, c.class_name, c.id as class_id, fa.fare, i.total_seats, i.booked_seats
        FROM schedules s
        JOIN flights f ON s.flight_id = f.id
        JOIN airlines a ON f.airline_id = a.id
        JOIN inventory i ON s.id = i.schedule_id
        JOIN classes c ON i.class_id = c.id
        JOIN fares fa ON s.id = fa.schedule_id AND c.id = fa.class_id
        WHERE s.origin_airport = ? AND s.destination_airport = ? AND DATE(s.departure_time) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $origin, $destination, $flight_date);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>Available Flights on <?php echo htmlspecialchars($flight_date); ?></h3>
<table border="1">
    <tr><th>Flight</th><th>Departs</th><th>Arrives</th><th>Class</th><th>Fare</th><th>Available Seats</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $available = $row['total_seats'] - $row['booked_seats']; ?>
        <?php if ($available > 0): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['airline_name']) . " (" . htmlspecialchars($row['flight_number']) . ")"; ?></td>
            <td><?php echo date('g:i A', strtotime($row['departure_time'])); ?></td>
            <td><?php echo date('g:i A', strtotime($row['arrival_time'])); ?></td>
            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['fare']); ?></td>
            <td><?php echo $available; ?></td>
            <td>
                <a href="book_ticket.php?schedule_id=<?php echo $row['schedule_id']; ?>&class_id=<?php echo $row['class_id']; ?>">Book Now</a>
            </td>
        </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>