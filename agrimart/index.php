<?php
include 'db_connect.php';
$products = $conn->query("SELECT p.*, f.farm_name FROM products p JOIN farmers f ON p.farmer_id = f.id WHERE p.stock_quantity > 0");
?>
<h1>Welcome to AgriMart</h1>
<?php if (isset($_SESSION['user_id'])): /* Nav links here */ endif; ?>
<hr>
<h3>Available Products</h3>
<?php while ($product = $products->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
        <p><strong>Sold by:</strong> <?php echo htmlspecialchars($product['farm_name']); ?></p>
        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
        <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
    </div>
<?php endwhile; ?>