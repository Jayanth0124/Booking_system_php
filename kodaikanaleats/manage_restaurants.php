<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_POST['add_rest'])) {
    $stmt = $conn->prepare("INSERT INTO restaurants (name, cuisine_type, location_area) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['cuisine'], $_POST['location']);
    $stmt->execute();
}
$restaurants = $conn->query("SELECT * FROM restaurants");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add New Restaurant</h3>
<form method="post">
    Name: <input type="text" name="name" required><br>
    Cuisine: <input type="text" name="cuisine"><br>
    Location: <input type="text" name="location"><br>
    <button type="submit" name="add_rest">Add Restaurant</button>
</form>
<hr>
<h3>Existing Restaurants</h3>
<table border="1">
    <?php while ($row = $restaurants->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
    </tr>
    <?php endwhile; ?>
</table>