<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$cart_items = $conn->query("SELECT p.name, p.price, c.quantity, p.id as product_id FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = " . $_SESSION['user_id']);
?>
<h1>Your Shopping Cart</h1>
<table border="1">
    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th></tr>
    <?php $total_amount = 0; while ($item = $cart_items->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
    </tr>
    <?php $total_amount += $item['price'] * $item['quantity']; endwhile; ?>
</table>
<h3>Grand Total: ₹<?php echo $total_amount; ?></h3>
<a href="checkout.php">Proceed to Checkout</a>