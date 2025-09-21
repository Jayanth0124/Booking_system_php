<?php
include 'db_connect.php';
// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a main category
if (isset($_POST['add_category'])) {
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $_POST['category_name']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}

// Handle adding a sub-category
if (isset($_POST['add_sub_category'])) {
    $stmt = $conn->prepare("INSERT INTO sub_categories (category_id, sub_category_name) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST['category_id'], $_POST['sub_category_name']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}

// Handle deleting categories
if (isset($_GET['delete_cat'])) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_cat']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}
if (isset($_GET['delete_subcat'])) {
    $stmt = $conn->prepare("DELETE FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_subcat']);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit();
}

// Fetch all categories and sub-categories for display
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
$sub_categories = $conn->query("SELECT sc.*, c.category_name FROM sub_categories sc JOIN categories c ON sc.category_id = c.id ORDER BY c.category_name, sc.sub_category_name");
?>

<!DOCTYPE html>
<html>
<head><title>Manage Categories</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Manage Product Categories</h2>

    <hr>
    <h3>Add New Main Category</h3>
    <form method="post">
        <input type="text" name="category_name" placeholder="e.g., Occasion" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>
    
    <h3>Add New Sub-Category</h3>
    <form method="post">
        Parent Category:
        <select name="category_id" required>
            <option value="">-- Select --</option>
            <?php mysqli_data_seek($categories, 0); while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
            <?php endwhile; ?>
        </select>
        Sub-Category Name:
        <input type="text" name="sub_category_name" placeholder="e.g., Birthday" required>
        <button type="submit" name="add_sub_category">Add Sub-Category</button>
    </form>
    <hr>

    <h3>Existing Main Categories</h3>
    <table border="1">
        <?php mysqli_data_seek($categories, 0); while ($cat = $categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
            <td><a href="?delete_cat=<?php echo $cat['id']; ?>" onclick="return confirm('WARNING: Deleting this will also delete all its sub-categories and products. Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Existing Sub-Categories</h3>
    <table border="1">
        <?php while ($sub_cat = $sub_categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($sub_cat['category_name'] . ' > ' . $sub_cat['sub_category_name']); ?></td>
            <td><a href="?delete_subcat=<?php echo $sub_cat['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>