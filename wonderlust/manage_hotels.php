<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_POST['add_hotel'])) {
    // Image upload logic would go here
    $stmt = $conn->prepare("INSERT INTO hotels (destination_id, hotel_name, address, price_per_night, available_rooms) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdi", $_POST['dest_id'], $_POST['name'], $_POST['address'], $_POST['price'], $_POST['rooms']);
    $stmt->execute();
}
$destinations = $conn->query("SELECT * FROM destinations");
$hotels = $conn->query("SELECT h.*, d.destination_name FROM hotels h JOIN destinations d ON h.destination_id = d.id");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add New Hotel</h3>
<form method="post" enctype="multipart/form-data">
    Destination: <select name="dest_id" required>
        <?php while ($row = $destinations->fetch_assoc()): ?><option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['destination_name']); ?></option><?php endwhile; ?>
    </select><br>
    Hotel Name: <input type="text" name="name" required><br>
    Address: <textarea name="address"></textarea><br>
    Price/Night: <input type="text" name="price" required><br>
    Available Rooms: <input type="number" name="rooms" required><br>
    Image: <input type="file" name="image"><br>
    <button type="submit" name="add_hotel">Add Hotel</button>
</form>
<hr>
<h3>Existing Hotels</h3>
<table border="1">
    <tr><th>Name</th><th>Destination</th><th>Price/Night</th></tr>
    <?php while ($row = $hotels->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
        <td><?php echo htmlspecialchars($row['destination_name']); ?></td>
        <td>₹<?php echo $row['price_per_night']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>