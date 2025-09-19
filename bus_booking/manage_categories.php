<?php
include 'db_connect.php';
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle form submissions for Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or Update Category
    if (isset($_POST['save_category'])) {
        $name = $_POST['name'];
        $id = $_POST['id'];
        if (empty($id)) {
            // Add new
            $stmt = $conn->prepare("INSERT INTO bus_categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
        } else {
            // Update existing
            $stmt = $conn->prepare("UPDATE bus_categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
        }
        $stmt->execute();
    }
}

// Handle Delete Category
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // Note: In a real system, you should check if this category is being used by any buses before deleting.
    $stmt = $conn->prepare("DELETE FROM bus_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_categories.php"); // Redirect to clean the URL
    exit();
}

// Fetch data for pre-filling the edit form
$category_to_edit = ['id' => '', 'name' => ''];
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM bus_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_to_edit = $result->fetch_assoc();
}

// Fetch all categories to display in the table
$result = $conn->query("SELECT * FROM bus_categories ORDER BY name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bus Categories</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Add / Edit Bus Category</h2>
    <form method="post" action="manage_categories.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($category_to_edit['id']); ?>">
        Category Name:
        <input type="text" name="name" value="<?php echo htmlspecialchars($category_to_edit['name']); ?>" required>
        <button type="submit" name="save_category">Save Category</button>
    </form>

    <hr>
    <h2>Existing Categories</h2>
    <table border="1">
        <tr>
            <th>Category Name</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>
                <a href="manage_categories.php?edit_id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="manage_categories.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>