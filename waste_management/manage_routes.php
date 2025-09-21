<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle creating a new route
if (isset($_POST['add_route'])) {
    $stmt = $conn->prepare("INSERT INTO routes (route_name) VALUES (?)");
    $stmt->bind_param("s", $_POST['route_name']);
    $stmt->execute();
}
// Handle assigning a bin to a route
if (isset($_POST['assign_bin'])) {
    $stmt = $conn->prepare("INSERT INTO route_bins (route_id, bin_id, collection_order) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_POST['route_id'], $_POST['bin_id'], $_POST['order']);
    $stmt->execute();
}
// Fetch data for forms and lists
$routes = $conn->query("SELECT * FROM routes");
$bins = $conn->query("SELECT * FROM bins");
$route_bins = $conn->query("SELECT rb.*, r.route_name, b.bin_location_name FROM route_bins rb JOIN routes r ON rb.route_id = r.id JOIN bins b ON rb.bin_id = b.id ORDER BY r.route_name, rb.collection_order");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Routes</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Collection Routes</h2>
    <hr>
    <h3>1. Create New Route</h3>
    <form method="post">
        Route Name: <input type="text" name="route_name" placeholder="e.g., North Zone Route A" required>
        <button type="submit" name="add_route">Create Route</button>
    </form>
    <hr>
    <h3>2. Assign Bin to Route</h3>
    <form method="post">
        Route: <select name="route_id" required>
            <?php mysqli_data_seek($routes, 0); while ($row = $routes->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['route_name']); ?></option><?php endwhile; ?>
        </select><br>
        Bin: <select name="bin_id" required>
            <?php while ($row = $bins->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['bin_location_name']); ?></option><?php endwhile; ?>
        </select><br>
        Collection Order (1, 2, 3...): <input type="number" name="order" required><br>
        <button type="submit" name="assign_bin">Assign Bin</button>
    </form>
    <hr>
    <h3>Current Route Assignments</h3>
    <table border="1">
        <tr><th>Route Name</th><th>Collection Order</th><th>Bin Location</th></tr>
        <?php while ($row = $route_bins->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['route_name']); ?></td>
            <td><?php echo $row['collection_order']; ?></td>
            <td><?php echo htmlspecialchars($row['bin_location_name']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>