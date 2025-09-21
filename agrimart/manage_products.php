<?php
include 'db_connect.php';

// 1. Security Check: Ensure the user is a logged-in farmer.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}

// 2. Get the unique Farmer ID from the logged-in User ID.
$user_id = $_SESSION['user_id'];
$farmer_result = $conn->query("SELECT id FROM farmers WHERE user_id = $user_id");
if ($farmer_result->num_rows == 0) {
    die("Error: Farmer profile not found for this user.");
}
$farmer_id = $farmer_result->fetch_assoc()['id'];


// =================================================================
//  HANDLE FORM SUBMISSIONS (ADD & UPDATE)
// =================================================================
if (isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $product_id = $_POST['product_id'];
    $image_path = $_POST['current_image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path); // Delete old image
        }
        $target_dir = "uploads/";
        $image_path = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    if (empty($product_id)) { // If no ID, it's a new product
        $stmt = $conn->prepare("INSERT INTO products (farmer_id, category_id, name, description, price, stock_quantity, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdsis", $farmer_id, $category_id, $name, $description, $price, $stock_quantity, $image_path);
    } else { // Otherwise, it's an update
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=?, image_path=? WHERE id=? AND farmer_id=?");
        $stmt->bind_param("ssdiisii", $name, $description, $price, $stock_quantity, $category_id, $image_path, $product_id, $farmer_id);
    }
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

// =================================================================
//  HANDLE DELETE REQUEST
// =================================================================
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    // First, get the image path to delete the file, ensuring it belongs to the farmer
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $id_to_delete, $farmer_id);
    $stmt->execute();
    if ($product = $stmt->get_result()->fetch_assoc()) {
        if (!empty($product['image_path']) && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }
    }
    // Now, delete the record from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $id_to_delete, $farmer_id);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

// =================================================================
//  FETCH DATA FOR DISPLAY & EDIT FORM
// =================================================================
$product_to_edit = ['id' => '', 'name' => '', 'description' => '', 'price' => '', 'stock_quantity' => '', 'category_id' => '', 'image_path' => ''];
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $_GET['edit_id'], $farmer_id);
    $stmt->execute();
    $product_to_edit = $stmt->get_result()->fetch_assoc();
}
$products = $conn->query("SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.farmer_id = $farmer_id ORDER BY p.id DESC");
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head><title>Manage My Products</title></head>
<body>
    <a href="farmer_dashboard.php">← Back to Dashboard</a>
    <h3><?php echo empty($product_to_edit['id']) ? 'Add New Product' : 'Edit Product'; ?></h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_to_edit['id']); ?>">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product_to_edit['image_path']); ?>">
        
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product_to_edit['name']); ?>" required><br><br>
        Description: <textarea name="description" required><?php echo htmlspecialchars($product_to_edit['description']); ?></textarea><br><br>
        Price (₹): <input type="text" name="price" value="<?php echo htmlspecialchars($product_to_edit['price']); ?>" required><br><br>
        Stock Quantity: <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($product_to_edit['stock_quantity']); ?>" required><br><br>
        
        Category: 
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product_to_edit['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        
        Image: <input type="file" name="image">
        <?php if (!empty($product_to_edit['image_path'])): ?>
            <p>Current Image: <img src="<?php echo htmlspecialchars($product_to_edit['image_path']); ?>" width="50"></p>
        <?php endif; ?>
        <br><br>
        <button type="submit" name="save_product">Save Product</button>
    </form>
    <hr>

    <h3>My Product Listings</h3>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
        <?php while ($product = $products->fetch_assoc()): ?>
        <tr>
            <td><img src="<?php echo htmlspecialchars($product['image_path']); ?>" width="50"></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
            <td>₹<?php echo $product['price']; ?></td>
            <td><?php echo $product['stock_quantity']; ?></td>
            <td>
                <a href="manage_products.php?edit_id=<?php echo $product['id']; ?>">Edit</a> | 
                <a href="manage_products.php?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>