<?php
include 'db_connect.php';
if (!isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    header("Location: index.php");
    exit();
}

$check_in_date = $_GET['check_in'];
$check_out_date = $_GET['check_out'];

// Find rooms that are NOT booked during the selected date range
$sql = "SELECT r.id, r.room_type, r.price, r.total_quantity, 
        (SELECT COUNT(*) FROM bookings b WHERE b.room_id = r.id AND b.status = 'Confirmed' AND NOT (b.check_out <= ? OR b.check_in >= ?)) as booked_quantity
        FROM rooms r";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $check_in_date, $check_out_date);
$stmt->execute();
$result = $stmt->get_result();
?>
<a href="index.php">Back to Home</a>
<h3>Available Rooms from <?php echo htmlspecialchars($check_in_date); ?> to <?php echo htmlspecialchars($check_out_date); ?></h3>

<table border="1">
    <tr>
        <th>Room Type</th>
        <th>Price per Night</th>
        <th>Available</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php $available_quantity = $row['total_quantity'] - $row['booked_quantity']; ?>
        <?php if ($available_quantity > 0): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo $available_quantity; ?></td>
            <td>
                <form action="book_room.php" method="post">
                    <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in_date); ?>">
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out_date); ?>">
                    <button type="submit">Book Now</button>
                </form>
            </td>
        </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>