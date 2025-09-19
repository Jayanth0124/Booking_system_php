<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if(isset($_POST['set_inventory'])) {
    $stmt = $conn->prepare("INSERT INTO inventory (schedule_id, class_id, total_seats) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE total_seats = VALUES(total_seats)");
    $stmt->bind_param("iii", $_POST['schedule_id'], $_POST['class_id'], $_POST['total_seats']);
    $stmt->execute();
}
$schedules = $conn->query("SELECT s.id, a.airline_name, f.flight_number, s.origin_airport, s.destination_airport FROM schedules s JOIN flights f ON s.flight_id = f.id JOIN airlines a ON f.airline_id = a.id");
$classes = $conn->query("SELECT * FROM classes");
?>
<h3>Set Seat Inventory</h3>
<form method="post">
    Schedule: <select name="schedule_id" required>
        <?php while($row = $schedules->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['airline_name'] . " " . $row['flight_number'] . " (" . $row['origin_airport'] . " - " . $row['destination_airport'] . ")"); ?></option>
        <?php endwhile; ?>
    </select><br>
    Class: <select name="class_id" required>
        <?php while($row = $classes->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['class_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Total Seats: <input type="number" name="total_seats" required><br>
    <button type="submit" name="set_inventory">Set Inventory</button>
</form>