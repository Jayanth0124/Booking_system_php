<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if(isset($_POST['set_availability'])) {
    $stmt = $conn->prepare("INSERT INTO seat_availability (route_id, class_id, journey_date, total_seats) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE total_seats = VALUES(total_seats)");
    $stmt->bind_param("iisi", $_POST['route_id'], $_POST['class_id'], $_POST['journey_date'], $_POST['total_seats']);
    $stmt->execute();
}
$routes = $conn->query("SELECT r.id, t.train_name, r.origin_station, r.destination_station FROM routes r JOIN trains t ON r.train_id = t.id");
$classes = $conn->query("SELECT * FROM classes");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Set Seat Availability for a Journey</h3>
<form method="post">
    Route: <select name="route_id" required>
        <?php while($row = $routes->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['train_name'] . " (" . $row['origin_station'] . " to " . $row['destination_station'] . ")"); ?></option>
        <?php endwhile; ?>
    </select><br>
    Class: <select name="class_id" required>
        <?php mysqli_data_seek($classes, 0); while($row = $classes->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['class_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Journey Date: <input type="date" name="journey_date" required><br>
    Total Seats: <input type="number" name="total_seats" required><br>
    <button type="submit" name="set_availability">Set/Update Availability</button>
</form>