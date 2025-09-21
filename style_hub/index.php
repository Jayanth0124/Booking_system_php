<?php
include 'db_connect.php';
$categories = $conn->query("SELECT * FROM categories");
$products = $conn->query("SELECT * FROM products WHERE stock_quantity > 0 LIMIT 8");
?>
<h1>Welcome to StyleHub</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="cart.php">My Cart</a> | <a href="my_orders.php">My Orders</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Shop by Category</h3>
<?php while ($cat = $categories->fetch_assoc()): ?>
    <h4><a href="products.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a></h4>
    <ul>
        <?php 
        $sub_cats = $conn->query("SELECT * FROM sub_categories WHERE category_id = " . $cat['id']);
        while ($sub_cat = $sub_cats->fetch_assoc()): ?>
            <li><a href="products.php?sub_category_id=<?php echo $sub_cat['id']; ?>"><?php echo htmlspecialchars($sub_cat['sub_category_name']); ?></a></li>
        <?php endwhile; ?>
    </ul>
<?php endwhile; ?>
<hr>
<h3>New Arrivals</h3>
<?php while ($product = $products->fetch_assoc()): ?>
    <div style="display: inline-block; width: 200px; border: 1px solid #ccc; padding: 10px; margin: 10px;">
        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="180">
        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
        <p>₹<?php echo htmlspecialchars($product['price']); ?></p>
        <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
    </div>
<?php endwhile; ?>