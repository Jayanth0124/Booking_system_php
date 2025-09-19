<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// =================================================================
//  HANDLE POST REQUESTS (ADD & UPDATE)
// =================================================================
if (isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $sub_category_id = $_POST['sub_category_id'];
    $product_id = $_POST['product_id'];

    $image_path = $_POST['current_image']; // Keep old image by default

    // Check if a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        // Delete old image if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
        // Upload new image
        $target_dir = "uploads/";
        $image_path = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    if (empty($product_id)) {
        // ADD NEW PRODUCT
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, sub_category_id, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiis", $name, $description, $price, $category_id, $sub_category_id, $image_path);
    } else {
        // UPDATE EXISTING PRODUCT
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, sub_category_id = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("ssdiisi", $name, $description, $price, $category_id, $sub_category_id, $image_path, $product_id);
    }
    $stmt->execute();
    header("Location: manage_products.php"); // Redirect to avoid form resubmission
    exit();
}

// =================================================================
//  HANDLE DELETE REQUEST
// =================================================================
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    
    // First, get the image path to delete the file
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($product = $result->fetch_assoc()) {
        if (!empty($product['image_path']) && file_exists($product['image_path'])) {
            unlink($product['image_path']); // Delete the image file
        }
    }
    
    // Now, delete the record from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

// =================================================================
//  FETCH DATA FOR DISPLAY & EDIT FORM
// =================================================================
$product_to_edit = ['id' => '', 'name' => '', 'description' => '', 'price' => '', 'category_id' => '', 'sub_category_id' => '', 'image_path' => ''];
if (isset($_GET['edit_id'])) {
    $id_to_edit = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_to_edit = $result->fetch_assoc();
}
$products = $conn->query("SELECT p.*, c.category_name, sc.sub_category_name FROM products p JOIN categories c ON p.category_id = c.id JOIN sub_categories sc ON p.sub_category_id = sc.id ORDER BY p.id DESC");
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h3><?php echo empty($product_to_edit['id']) ? 'Add New Cake' : 'Edit Cake'; ?></h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_to_edit['id']); ?>">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product_to_edit['image_path']); ?>">
        
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product_to_edit['name']); ?>" required><br><br>
        Description: <textarea name="description" required><?php echo htmlspecialchars($product_to_edit['description']); ?></textarea><br><br>
        Price: <input type="text" name="price" value="<?php echo htmlspecialchars($product_to_edit['price']); ?>" required><br><br>
        Category: 
        <select name="category_id" required>
            <?php mysqli_data_seek($categories, 0); while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product_to_edit['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        Sub-Category: 
        <select name="sub_category_id" required>
             <?php $sub_categories = $conn->query("SELECT * FROM sub_categories"); while($sub_cat = $sub_categories->fetch_assoc()): ?>
                <option value="<?php echo $sub_cat['id']; ?>" <?php if($sub_cat['id'] == $product_to_edit['sub_category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($sub_cat['sub_category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        Image: <input type="file" name="image">
        <?php if (!empty($product_to_edit['image_path'])): ?>
            <p>Current: <img src="<?php echo htmlspecialchars($product_to_edit['image_path']); ?>" width="50"></p>
        <?php endif; ?>
        <br><br>
        <button type="submit" name="save_product">Save Product</button>
    </form>
    <hr>
    <h3>Existing Cakes</h3>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr>
        <?php while ($product = $products->fetch_assoc()): ?>
        <tr>
            <td><img src="<?php echo htmlspecialchars($product['image_path']); ?>" width="50"></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['category_name'] . " - " . $product['sub_category_name']); ?></td>
            <td>₹<?php echo $product['price']; ?></td>
            <td>
                <a href="manage_products.php?edit_id=<?php echo $product['id']; ?>">Edit</a> | 
                <a href="manage_products.php?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>