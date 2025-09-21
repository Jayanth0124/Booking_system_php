<?php
include 'db_connect.php';
// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add/Update Food Item
if (isset($_POST['save_food'])) {
    $name = $_POST['food_name'];
    $desc = $_POST['description'];
    $dest_id = $_POST['destination_id'];
    $id = $_POST['id'];
    $image_path = $_POST['current_image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if (!empty($image_path) && file_exists($image_path)) { unlink($image_path); }
        $target_dir = "uploads/";
        $image_path = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO foods (destination_id, food_name, description, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $dest_id, $name, $desc, $image_path);
    } else {
        // Update logic would go here
    }
    $stmt->execute();
    header("Location: manage_foods.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    // First delete image file, then the record
    $stmt = $conn->prepare("SELECT image_path FROM foods WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']); $stmt->execute();
    if($food = $stmt->get_result()->fetch_assoc()){
        if(!empty($food['image_path']) && file_exists($food['image_path'])) unlink($food['image_path']);
    }
    $stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_foods.php");
    exit();
}

// Fetch data for forms and display
$destinations = $conn->query("SELECT * FROM destinations");
$foods = $conn->query("SELECT f.*, d.destination_name FROM foods f JOIN destinations d ON f.destination_id = d.id");
?>

<!DOCTYPE html>
<html>
<head><title>Manage Food Details</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Food Details</h2>

    <h3>Add New Food Item</h3>
    <form method="post" enctype="multipart/form-data">
        Destination:
        <select name="destination_id" required>
            <option value="">-- Select Destination --</option>
            <?php while ($dest = $destinations->fetch_assoc()): ?>
            <option value="<?php echo $dest['id']; ?>"><?php echo htmlspecialchars($dest['destination_name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>
        Food Name: <input type="text" name="food_name" required><br><br>
        Description: <textarea name="description"></textarea><br><br>
        Image: <input type="file" name="image"><br><br>
        <button type="submit" name="save_food">Save Food Item</button>
    </form>
    <hr>
    <h3>Existing Food Items</h3>
    <table border="1">
        <tr><th>Image</th><th>Name</th><th>Destination</th><th>Action</th></tr>
        <?php while ($row = $foods->fetch_assoc()): ?>
        <tr>
            <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>" width="50"></td>
            <td><?php echo htmlspecialchars($row['food_name']); ?></td>
            <td><?php echo htmlspecialchars($row['destination_name']); ?></td>
            <td><a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>