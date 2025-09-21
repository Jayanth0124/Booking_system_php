<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
// Handle removing an item
if (isset($_GET['remove_id'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['remove_id'], $user_id);
    $stmt->execute();
}
$cart_items = $conn->query("SELECT c.id, p.name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
?>
<h1>Your Cart</h1>
<table border="1">
    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr>
    <?php $grand_total = 0; while ($item = $cart_items->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
        <td><a href="?remove_id=<?php echo $item['id']; ?>">Remove</a></td>
    </tr>
    <?php $grand_total += $item['price'] * $item['quantity']; endwhile; ?>
</table>
<h3>Grand Total: ₹<?php echo $grand_total; ?></h3>
<a href="checkout.php">Proceed to Checkout</a>