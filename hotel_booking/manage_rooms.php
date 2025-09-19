<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $price = $_POST['price'];
    $quantity = $_POST['total_quantity'];
    $stmt = $conn->prepare("UPDATE rooms SET price = ?, total_quantity = ? WHERE id = ?");
    $stmt->bind_param("dii", $price, $quantity, $room_id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM rooms");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Manage Rooms</h3>
<table border="1">
    <tr><th>Room Type</th><th>Price</th><th>Total Quantity</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <form method="post">
            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
            <td><input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>"></td>
            <td><input type="number" name="total_quantity" value="<?php echo $row['total_quantity']; ?>"></td>
            <td>
                <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                <button type="submit">Update</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>