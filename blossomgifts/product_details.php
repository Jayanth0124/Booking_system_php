<?php
include 'db_connect.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$product_id = $_GET['id'];
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $product_id, $_POST['quantity']);
    $stmt->execute();
    header("Location: cart.php");
}
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id); $stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
?>
<h1><?php echo htmlspecialchars($product['name']); ?></h1>
<img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="300">
<p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
<p><strong>Availability:</strong> <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
<p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
<?php if ($product['stock_quantity'] > 0): ?>
<form method="post">
    Quantity: <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>"><br>
    <button type="submit" name="add_to_cart">Add to Cart</button>
</form>
<?php endif; ?>
<a href="index.php">Back to Shop</a>