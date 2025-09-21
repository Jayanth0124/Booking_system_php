<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle adding a category
if (isset($_POST['add_category'])) {
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $_POST['category_name']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}
// Handle deleting a category
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Categories</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Product Categories</h2>
    <form method="post">
        <input type="text" name="category_name" placeholder="e.g., Fertilizers" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>
    <hr>
    <h3>Existing Categories</h3>
    <table border="1">
        <?php while ($cat = $categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
            <td><a href="?delete_id=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>