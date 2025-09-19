<?php
include 'db_connect.php';
// Check if the user is an admin or staff
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'customer') {
    header("Location: index.php");
    exit();
}

// Handle Add & Update
if (isset($_POST['save_service'])) {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $duration = $_POST['duration_minutes'];
    $price = $_POST['price'];
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO services (service_name, description, duration_minutes, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $desc, $duration, $price);
    } else {
        $stmt = $conn->prepare("UPDATE services SET service_name=?, description=?, duration_minutes=?, price=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $desc, $duration, $price, $id);
    }
    $stmt->execute();
    header("Location: manage_services.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_services.php");
    exit();
}

// Fetch a service for editing
$service_to_edit = ['id' => '', 'service_name' => '', 'description' => '', 'duration_minutes' => '', 'price' => ''];
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $service_to_edit = $stmt->get_result()->fetch_assoc();
}

// Fetch all services
$services = $conn->query("SELECT * FROM services ORDER BY service_name");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Services</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h3><?php echo empty($service_to_edit['id']) ? 'Add New Service' : 'Edit Service'; ?></h3>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($service_to_edit['id']); ?>">
        Name: <input type="text" name="service_name" value="<?php echo htmlspecialchars($service_to_edit['service_name']); ?>" required><br>
        Description: <textarea name="description"><?php echo htmlspecialchars($service_to_edit['description']); ?></textarea><br>
        Duration (mins): <input type="number" name="duration_minutes" value="<?php echo htmlspecialchars($service_to_edit['duration_minutes']); ?>" required><br>
        Price (₹): <input type="text" name="price" value="<?php echo htmlspecialchars($service_to_edit['price']); ?>" required><br>
        <button type="submit" name="save_service">Save Service</button>
    </form>
    <hr>
    <h3>Existing Services</h3>
    <table border="1">
        <tr><th>Name</th><th>Duration</th><th>Price</th><th>Actions</th></tr>
        <?php while ($row = $services->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
            <td><?php echo $row['duration_minutes']; ?> mins</td>
            <td>₹<?php echo $row['price']; ?></td>
            <td>
                <a href="?edit_id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>