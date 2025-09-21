<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_POST['add_driver'])) {
    $conn->begin_transaction();
    try {
        $stmt_drv = $conn->prepare("INSERT INTO drivers (full_name, vehicle_number) VALUES (?, ?)");
        $stmt_drv->bind_param("ss", $_POST['full_name'], $_POST['vehicle_number']);
        $stmt_drv->execute();
        $driver_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, related_id) VALUES (?, ?, 'driver', ?)");
        $stmt_user->bind_param("ssi", $_POST['username'], $_POST['password'], $driver_id);
        $stmt_user->execute();
        $conn->commit();
    } catch (Exception $e) { $conn->rollback(); }
}
$drivers = $conn->query("SELECT * FROM drivers");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add New Driver</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Vehicle Number: <input type="text" name="vehicle_number" required><br>
    Login Username: <input type="text" name="username" required><br>
    Password: <input type="text" name="password" required><br>
    <button type="submit" name="add_driver">Add Driver</button>
</form>
<hr>
<h3>Existing Drivers</h3>
<table border="1">
    <tr><th>Name</th><th>Vehicle No.</th></tr>
    <?php while ($row = $drivers->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['vehicle_number']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>