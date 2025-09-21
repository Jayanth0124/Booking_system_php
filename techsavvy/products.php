<?php
include 'db_connect.php';

// Check if a category ID was provided, otherwise redirect to the homepage.
if (!isset($_GET['category_id'])) {
    header("Location: index.php");
    exit();
}

$category_id = $_GET['category_id'];

// Fetch the category name to display as a title
$cat_stmt = $conn->prepare("SELECT category_name FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$category = $cat_stmt->get_result()->fetch_assoc();
$category_name = $category ? $category['category_name'] : 'Unknown Category';

// Fetch all products belonging to this category
$prod_stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND stock_quantity > 0");
$prod_stmt->bind_param("i", $category_id);
$prod_stmt->execute();
$products = $prod_stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Products - <?php echo htmlspecialchars($category_name); ?></title>
</head>
<body>
    <h1>TechSavvy</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="cart.php">My Cart</a> | <a href="my_orders.php">My Orders</a> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
    <?php endif; ?>
    <hr>
    <a href="index.php">← Back to All Categories</a>
    <h2>Showing Products in: <?php echo htmlspecialchars($category_name); ?></h2>

    <div>
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px; display: inline-block; width: 200px; vertical-align: top;">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="width:100%; height: auto;">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </div>

</body>
</html>