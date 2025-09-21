<?php
include 'db_connect.php';
// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a new offer
if (isset($_POST['add_offer'])) {
    $stmt = $conn->prepare("INSERT INTO special_offers (restaurant_id, offer_title, offer_description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_POST['restaurant_id'], $_POST['offer_title'], $_POST['offer_description']);
    $stmt->execute();
    header("Location: manage_offers.php");
    exit();
}

// Handle deleting an offer
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM special_offers WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_offers.php");
    exit();
}

// Fetch data for the form and the display list
$restaurants = $conn->query("SELECT * FROM restaurants ORDER BY name");
$offers = $conn->query("SELECT so.*, r.name as restaurant_name FROM special_offers so JOIN restaurants r ON so.restaurant_id = r.id ORDER BY r.name");
?>

<!DOCTYPE html>
<html>
<head><title>Manage Special Offers</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Special Offers</h2>

    <h3>Add New Offer</h3>
    <form method="post">
        Restaurant:
        <select name="restaurant_id" required>
            <option value="">-- Select Restaurant --</option>
            <?php while ($rest = $restaurants->fetch_assoc()): ?>
            <option value="<?php echo $rest['id']; ?>"><?php echo htmlspecialchars($rest['name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>
        Offer Title: <input type="text" name="offer_title" required size="50"><br><br>
        Description: <textarea name="offer_description" rows="3" cols="50"></textarea><br><br>
        <button type="submit" name="add_offer">Add Offer</button>
    </form>
    <hr>

    <h3>Existing Special Offers</h3>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Restaurant</th>
                <th>Offer Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $offers->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                <td><?php echo htmlspecialchars($row['offer_title']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['offer_description'])); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>