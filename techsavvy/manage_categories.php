<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a category
if (isset($_POST['add_category'])) {
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $_POST['category_name']);
    $stmt->execute();
}

// Handle adding a sub-category
if (isset($_POST['add_sub_category'])) {
    $stmt = $conn->prepare("INSERT INTO sub_categories (category_id, sub_category_name) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST['category_id'], $_POST['sub_category_name']);
    $stmt->execute();
}

// Handle deleting
if (isset($_GET['delete_cat'])) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_cat']);
    $stmt->execute();
}
if (isset($_GET['delete_subcat'])) {
    $stmt = $conn->prepare("DELETE FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_subcat']);
    $stmt->execute();
}

$categories = $conn->query("SELECT * FROM categories");
$sub_categories = $conn->query("SELECT sc.*, c.category_name FROM sub_categories sc JOIN categories c ON sc.category_id = c.id");
?>

<!DOCTYPE html>
<html>
<head><title>Manage Categories</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Manage Categories</h2>

    <hr>
    <h3>Add New Category</h3>
    <form method="post">
        <input type="text" name="category_name" placeholder="e.g., Smartphones" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>
    
    <h3>Add New Sub-Category</h3>
    <form method="post">
        <select name="category_id" required>
            <option value="">-- Select Parent Category --</option>
            <?php mysqli_data_seek($categories, 0); while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="sub_category_name" placeholder="e.g., Android" required>
        <button type="submit" name="add_sub_category">Add Sub-Category</button>
    </form>
    <hr>

    <h3>Existing Categories</h3>
    <table border="1">
        <?php mysqli_data_seek($categories, 0); while ($cat = $categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
            <td><a href="?delete_cat=<?php echo $cat['id']; ?>" onclick="return confirm('Sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Existing Sub-Categories</h3>
    <table border="1">
        <?php while ($sub_cat = $sub_categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($sub_cat['category_name'] . ' > ' . $sub_cat['sub_category_name']); ?></td>
            <td><a href="?delete_subcat=<?php echo $sub_cat['id']; ?>" onclick="return confirm('Sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>