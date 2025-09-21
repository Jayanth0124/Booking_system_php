<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_POST['add_table'])) {
    $stmt = $conn->prepare("INSERT INTO table_types (restaurant_id, type_name, capacity, quantity, base_booking_fee) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiid", $_POST['rest_id'], $_POST['type_name'], $_POST['capacity'], $_POST['quantity'], $_POST['fee']);
    $stmt->execute();
}
$restaurants = $conn->query("SELECT * FROM restaurants");
$tables = $conn->query("SELECT tt.*, r.name as restaurant_name FROM table_types tt JOIN restaurants r ON tt.restaurant_id = r.id");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add Table Type to a Restaurant</h3>
<form method="post">
    Restaurant: <select name="rest_id" required>
        <?php while ($row = $restaurants->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option><?php endwhile; ?>
    </select><br>
    Table Type Name: <input type="text" name="type_name" placeholder="e.g., 4-Seater Outdoor" required><br>
    Capacity (Guests): <input type="number" name="capacity" required><br>
    Quantity (No. of such tables): <input type="number" name="quantity" required><br>
    Booking Fee (₹): <input type="text" name="fee" required><br>
    <button type="submit" name="add_table">Add Table Type</button>
</form>
<hr>
<h3>Existing Table Inventory</h3>
<table border="1">
    <tr><th>Restaurant</th><th>Table Type</th><th>Capacity</th><th>Quantity</th><th>Fee</th></tr>
    <?php while ($row = $tables->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
        <td><?php echo $row['capacity']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>₹<?php echo $row['base_booking_fee']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>