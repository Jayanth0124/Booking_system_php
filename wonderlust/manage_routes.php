<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle Add/Update Route
if (isset($_POST['save_route'])) {
    $dest_id = $_POST['destination_id'];
    $title = $_POST['route_title'];
    $desc = $_POST['description'];
    $map_link = $_POST['map_link'];
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO routes (destination_id, route_title, description, map_link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $dest_id, $title, $desc, $map_link);
    } else {
        // Update logic would go here
    }
    $stmt->execute();
    header("Location: manage_routes.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM routes WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_routes.php");
    exit();
}

// Fetch data for forms and display
$destinations = $conn->query("SELECT * FROM destinations");
$routes = $conn->query("SELECT r.*, d.destination_name FROM routes r JOIN destinations d ON r.destination_id = d.id");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Route Details</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Route & Itinerary Details</h2>

    <h3>Add New Route/Itinerary</h3>
    <form method="post">
        Destination:
        <select name="destination_id" required>
            <option value="">-- Select Destination --</option>
            <?php while ($dest = $destinations->fetch_assoc()): ?>
            <option value="<?php echo $dest['id']; ?>"><?php echo htmlspecialchars($dest['destination_name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>
        Route Title (e.g., Day 1: City Tour): <input type="text" name="route_title" required size="50"><br><br>
        Description: <textarea name="description" rows="5" cols="50"></textarea><br><br>
        Google Maps Link (Optional): <input type="text" name="map_link" size="50"><br><br>
        <button type="submit" name="save_route">Save Route</button>
    </form>
    <hr>
    <h3>Existing Routes</h3>
    <table border="1">
        <tr><th>Title</th><th>Destination</th><th>Action</th></tr>
        <?php while ($row = $routes->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['route_title']); ?></td>
            <td><?php echo htmlspecialchars($row['destination_name']); ?></td>
            <td><a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>