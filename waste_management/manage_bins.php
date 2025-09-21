<?php
include 'db_connect.php';
// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add & Update
if (isset($_POST['save_bin'])) {
    $name = $_POST['bin_location_name'];
    $lat = $_POST['latitude'];
    $long = $_POST['longitude'];
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO bins (bin_location_name, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $lat, $long);
    } else {
        $stmt = $conn->prepare("UPDATE bins SET bin_location_name=?, latitude=?, longitude=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $lat, $long, $id);
    }
    $stmt->execute();
    header("Location: manage_bins.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM bins WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_bins.php");
    exit();
}

// Fetch a bin for editing
$bin_to_edit = ['id' => '', 'bin_location_name' => '', 'latitude' => '', 'longitude' => ''];
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM bins WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $bin_to_edit = $stmt->get_result()->fetch_assoc();
}

// Fetch all bins
$bins = $conn->query("SELECT * FROM bins ORDER BY bin_location_name");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Garbage Bins</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h3><?php echo empty($bin_to_edit['id']) ? 'Create New Garbage Bin' : 'Edit Garbage Bin'; ?></h3>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($bin_to_edit['id']); ?>">
        Location Name: <input type="text" name="bin_location_name" value="<?php echo htmlspecialchars($bin_to_edit['bin_location_name']); ?>" required><br>
        Latitude: <input type="text" name="latitude" value="<?php echo htmlspecialchars($bin_to_edit['latitude']); ?>"><br>
        Longitude: <input type="text" name="longitude" value="<?php echo htmlspecialchars($bin_to_edit['longitude']); ?>"><br>
        <em><small>On Google Maps, right-click a location to get its Latitude and Longitude.</small></em><br>
        <button type="submit" name="save_bin">Save Bin</button>
    </form>
    <hr>
    <h3>Existing Bins</h3>
    <table border="1">
        <tr><th>Location Name</th><th>Coordinates</th><th>Status</th><th>Actions</th></tr>
        <?php while ($row = $bins->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['bin_location_name']); ?></td>
            <td><?php echo htmlspecialchars($row['latitude'] . ', ' . $row['longitude']); ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="?edit_id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>