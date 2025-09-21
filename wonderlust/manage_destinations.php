<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// --- NEW CODE BLOCK TO HANDLE DELETE ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    // Redirect to the same page to refresh the list
    header("Location: manage_destinations.php");
    exit();
}
// --- END OF NEW CODE BLOCK ---

if (isset($_POST['add_dest'])) {
    $stmt = $conn->prepare("INSERT INTO destinations (destination_name, description, best_season_to_visit) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['desc'], $_POST['season']);
    $stmt->execute();
    // Also redirect after adding to prevent re-submission
    header("Location: manage_destinations.php");
    exit();
}
$destinations = $conn->query("SELECT * FROM destinations");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add New Destination</h3>
<form method="post">
    Name: <input type="text" name="name" required><br>
    Description: <textarea name="desc"></textarea><br>
    Best Season: <input type="text" name="season"><br>
    <button type="submit" name="add_dest">Add Destination</button>
</form>
<hr>
<h3>Existing Destinations</h3>
<table border="1">
    <?php while ($row = $destinations->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['destination_name']); ?></td>
        <td>
            <a href="#">Edit</a> | 
            <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this destination?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>