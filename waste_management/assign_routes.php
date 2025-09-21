<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if(isset($_POST['assign_route'])) {
    $stmt = $conn->prepare("INSERT INTO driver_assignments (driver_id, route_id, assignment_date) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE route_id = VALUES(route_id)");
    $stmt->bind_param("iis", $_POST['driver_id'], $_POST['route_id'], $_POST['assignment_date']);
    $stmt->execute();
    echo "<b>Route assigned successfully!</b>";
}
$drivers = $conn->query("SELECT * FROM drivers");
$routes = $conn->query("SELECT * FROM routes");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Assign Daily Route for a Driver</h3>
<form method="post">
    Date: <input type="date" name="assignment_date" value="<?php echo date('Y-m-d'); ?>" required><br>
    Driver: <select name="driver_id" required>
        <?php while ($row = $drivers->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['full_name']); ?></option><?php endwhile; ?>
    </select><br>
    Route: <select name="route_id" required>
        <?php while ($row = $routes->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['route_name']); ?></option><?php endwhile; ?>
    </select><br>
    <button type="submit" name="assign_route">Assign Route</button>
</form>