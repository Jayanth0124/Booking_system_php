<?php
// This is a comprehensive manage products page with full Add/Edit/Delete functionality.
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }

// Handle Add & Update
if (isset($_POST['save_product'])) {
    // ... (This logic is identical to the final version of `manage_products.php` from the "techsavvy" project)
    // For brevity, I will reuse the same robust logic here.
    $name = $_POST['name']; $desc = $_POST['description']; $price = $_POST['price']; $stock = $_POST['stock_quantity']; $cat_id = $_POST['category_id']; $subcat_id = $_POST['sub_category_id']; $id = $_POST['product_id'];
    $image_path = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if (!empty($image_path) && file_exists($image_path)) { unlink($image_path); }
        $target_dir = "uploads/";
        $image_path = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }
    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, sub_category_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiiss", $name, $desc, $price, $stock, $cat_id, $subcat_id, $image_path);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=?, sub_category_id=?, image_path=? WHERE id=?");
        $stmt->bind_param("ssdiissi", $name, $desc, $price, $stock, $cat_id, $subcat_id, $image_path, $id);
    }
    $stmt->execute();
    header("Location: manage_products.php"); exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete); $stmt->execute();
    if ($product = $stmt->get_result()->fetch_assoc()) {
        if (!empty($product['image_path']) && file_exists($product['image_path'])) { unlink($product['image_path']); }
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete); $stmt->execute();
    header("Location: manage_products.php"); exit();
}

// Fetch data for form
$product_to_edit = ['id' => '', 'name' => '', 'description' => '', 'price' => '', 'stock_quantity' => '', 'category_id' => '', 'sub_category_id' => '', 'image_path' => ''];
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']); $stmt->execute();
    $product_to_edit = $stmt->get_result()->fetch_assoc();
}
$products = $conn->query("SELECT p.*, c.category_name, sc.sub_category_name FROM products p JOIN categories c ON p.category_id = c.id JOIN sub_categories sc ON p.sub_category_id = sc.id ORDER BY p.id DESC");
$categories = $conn->query("SELECT * FROM categories");
?>
<h3><?php echo empty($product_to_edit['id']) ? 'Add New Product' : 'Edit Product'; ?></h3>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_to_edit['id']); ?>">
    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product_to_edit['image_path']); ?>">
    Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product_to_edit['name']); ?>" required><br>
    Description: <textarea name="description" required><?php echo htmlspecialchars($product_to_edit['description']); ?></textarea><br>
    Price: <input type="text" name="price" value="<?php echo htmlspecialchars($product_to_edit['price']); ?>" required><br>
    Stock: <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($product_to_edit['stock_quantity']); ?>" required><br>
    Category: <select name="category_id" required>
        <?php while($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product_to_edit['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Sub-Category: <select name="sub_category_id" required>
        <?php $sub_categories = $conn->query("SELECT * FROM sub_categories"); while($sub_cat = $sub_categories->fetch_assoc()): ?>
            <option value="<?php echo $sub_cat['id']; ?>" <?php if($sub_cat['id'] == $product_to_edit['sub_category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($sub_cat['sub_category_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Image: <input type="file" name="image"><br>
    <button type="submit" name="save_product">Save Product</button>
</form>
<hr>
<h3>Existing Products</h3>
<table border="1">
    <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
    <?php while ($product = $products->fetch_assoc()): ?>
    <tr>
        <td><img src="<?php echo htmlspecialchars($product['image_path']); ?>" width="50"></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td><?php echo htmlspecialchars($product['category_name'] . " > " . $product['sub_category_name']); ?></td>
        <td>₹<?php echo $product['price']; ?></td>
        <td><?php echo $product['stock_quantity']; ?></td>
        <td>
            <a href="?edit_id=<?php echo $product['id']; ?>">Edit</a> | 
            <a href="?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>