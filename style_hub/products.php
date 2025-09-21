<?php
include 'db_connect.php';
$sql = "SELECT * FROM products WHERE stock_quantity > 0";
$page_title = "All Products";
if (isset($_GET['category_id'])) {
    $sql .= " AND category_id = " . (int)$_GET['category_id'];
    $page_title = $conn->query("SELECT category_name FROM categories WHERE id = ".(int)$_GET['category_id'])->fetch_assoc()['category_name'];
}
if (isset($_GET['sub_category_id'])) {
    $sql .= " AND sub_category_id = " . (int)$_GET['sub_category_id'];
     $page_title = $conn->query("SELECT sub_category_name FROM sub_categories WHERE id = ".(int)$_GET['sub_category_id'])->fetch_assoc()['sub_category_name'];
}
$products = $conn->query($sql);
?>
<a href="index.php">Home</a>
<h2><?php echo htmlspecialchars($page_title); ?></h2>
<?php while ($product = $products->fetch_assoc()): ?>
    <div style="display: inline-block; width: 200px; border: 1px solid #ccc; padding: 10px; margin: 10px;">
        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="180">
        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
        <p>₹<?php echo htmlspecialchars($product['price']); ?></p>
        <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
    </div>
<?php endwhile; ?>