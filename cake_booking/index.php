<?php
include 'db_connect.php';
// Build the WHERE clause for filtering
$where_clauses = [];
$params = [];
$types = '';
if (!empty($_GET['category'])) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $_GET['category'];
    $types .= 'i';
}
if (!empty($_GET['sub_category'])) {
    $where_clauses[] = "p.sub_category_id = ?";
    $params[] = $_GET['sub_category'];
    $types .= 'i';
}
if (!empty($_GET['min_price'])) {
    $where_clauses[] = "p.price >= ?";
    $params[] = $_GET['min_price'];
    $types .= 'd';
}
if (!empty($_GET['max_price'])) {
    $where_clauses[] = "p.price <= ?";
    $params[] = $_GET['max_price'];
    $types .= 'd';
}
$sql = "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.id";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
$categories = $conn->query("SELECT * FROM categories");
$sub_categories = $conn->query("SELECT * FROM sub_categories");
?>
<h1>Welcome to the Cake Shop!</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="cart.php">My Cart</a> | <a href="my_orders.php">My Orders</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<div style="float:left; width: 20%;">
    <h3>Filter Cakes</h3>
    <form method="get">
        Category:
        <select name="category">
            <option value="">All</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
            <?php endwhile; ?>
        </select><br>
        Sub-Category:
        <select name="sub_category">
             <option value="">All</option>
            <?php while ($sub_cat = $sub_categories->fetch_assoc()): ?>
                <option value="<?php echo $sub_cat['id']; ?>"><?php echo htmlspecialchars($sub_cat['sub_category_name']); ?></option>
            <?php endwhile; ?>
        </select><br>
        Price Range:
        <input type="number" name="min_price" placeholder="Min"> - <input type="number" name="max_price" placeholder="Max"><br>
        <button type="submit">Filter</button>
    </form>
</div>
<div style="float:left; width: 75%; padding-left: 5%;">
    <?php while ($product = $products->fetch_assoc()): ?>
        <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Cake Image" width="150">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
        </div>
    <?php endwhile; ?>
</div>