<?php
include 'db_connect.php';
$categories = $conn->query("SELECT * FROM categories");
$products = $conn->query("SELECT * FROM products WHERE stock_quantity > 0 LIMIT 10");
?>
<h1>Welcome to TechSavvy</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="cart.php">My Cart</a> | <a href="my_orders.php">My Orders</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Shop by Category</h3>
<ul>
    <?php while ($cat = $categories->fetch_assoc()): ?>
        <li><a href="products.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a></li>
    <?php endwhile; ?>
</ul>
<hr>
<h3>Featured Products</h3>
<?php while ($product = $products->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="100">
        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
        <p>Price: ₹<?php echo htmlspecialchars($product['price']); ?></p>
        <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
    </div>
<?php endwhile; ?>