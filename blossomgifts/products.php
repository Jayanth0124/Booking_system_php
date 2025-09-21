<?php
include 'db_connect.php';

$query_sql = "SELECT p.*, c.category_name, sc.sub_category_name FROM products p 
              JOIN categories c ON p.category_id = c.id 
              JOIN sub_categories sc ON p.sub_category_id = sc.id 
              WHERE p.stock_quantity > 0";
$query_params = [];
$query_types = "";
$title = "All Products";

// Check if a category filter is applied
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $query_sql .= " AND p.category_id = ?";
    $query_params[] = $category_id;
    $query_types .= "i";

    // Fetch the category name for the title
    $stmt_title = $conn->prepare("SELECT category_name FROM categories WHERE id = ?");
    $stmt_title->bind_param("i", $category_id);
    $stmt_title->execute();
    $result_title = $stmt_title->get_result();
    if ($cat = $result_title->fetch_assoc()) {
        $title = htmlspecialchars($cat['category_name']) . " Gifts";
    }

}
// Check if a sub-category filter is applied
else if (isset($_GET['sub_category_id'])) {
    $sub_category_id = $_GET['sub_category_id'];
    $query_sql .= " AND p.sub_category_id = ?";
    $query_params[] = $sub_category_id;
    $query_types .= "i";

    // Fetch the sub-category name for the title
    $stmt_title = $conn->prepare("SELECT sub_category_name FROM sub_categories WHERE id = ?");
    $stmt_title->bind_param("i", $sub_category_id);
    $stmt_title->execute();
    $result_title = $stmt_title->get_result();
    if ($sub_cat = $result_title->fetch_assoc()) {
        $title = htmlspecialchars($sub_cat['sub_category_name']) . " Gifts";
    }
}

// Prepare and execute the main product query
$stmt = $conn->prepare($query_sql);
if (!empty($query_types)) {
    $stmt->bind_param($query_types, ...$query_params);
}
$stmt->execute();
$products = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
</head>
<body>
    <a href="index.php">Back to Shop</a>
    <h1><?php echo $title; ?></h1>
    
    <?php if ($products->num_rows > 0): ?>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div style="width: 200px; border: 1px solid #ccc; padding: 10px;">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="180">
                    <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p><b>Category:</b> <?php echo htmlspecialchars($product['category_name'] . " > " . $product['sub_category_name']); ?></p>
                    <p><b>Price:</b> ₹<?php echo htmlspecialchars($product['price']); ?></p>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No products found for this selection.</p>
    <?php endif; ?>
</body>
</html>